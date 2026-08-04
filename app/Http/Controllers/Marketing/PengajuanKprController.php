<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Enums\StatusPenjualan;
use App\Http\Controllers\Controller;
use App\Http\Requests\PengajuanKprRequest;
use App\Models\Booking;
use App\Models\PengajuanKpr;
use App\Services\DokumenService;
use App\Services\PengajuanKprService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class PengajuanKprController extends Controller
{
    public function __construct(
        private readonly PengajuanKprService $pengajuanKprService,
        private readonly DokumenService $dokumenService
    ) {}

    public function index(Request $request): View
    {
        $idMarketing = Auth::id();

        $query = PengajuanKpr::query()
            ->whereHas('booking', function ($q) use ($idMarketing) {
                $q->where('id_marketing', $idMarketing);
            })
            ->with(['konsumen', 'booking', 'unit']);

        $search = $request->input('search', '');

        if ($search !== '') {
            $term = '%'.$search.'%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('konsumen', function ($q) use ($term) {
                    $q->where('nama_lengkap', 'like', $term)
                        ->orWhere('nik', 'like', $term);
                })->orWhere('nama_bank', 'like', $term);
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        $pengajuans = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        return view('marketing.pengajuan-kpr.index', compact('pengajuans', 'search', 'perPage'));
    }

    public function store(PengajuanKprRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['tanggal_pengajuan'] = $validated['tanggal_pengajuan'] ?? now()->toDateString();
        $validated['status_pengajuan'] = 'draft';

        try {
            $this->pengajuanKprService->createForBooking($validated, $validated['id_booking']);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        return redirect()->route('marketing.pengajuan-kpr.index')
            ->with('success', 'Pengajuan KPR berhasil disimpan sebagai draft.');
    }

    public function create(): View
    {
        $idMarketing = Auth::id();

        $bookingOptions = Booking::query()
            ->where('id_marketing', $idMarketing)
            ->whereDoesntHave('pengajuanKpr')
            ->whereHas('statusPenjualan', function ($q) {
                $q->where('status_saat_ini', StatusPenjualan::Booking->value);
            })
            ->with(['konsumen', 'unit'])
            ->orderByDesc('created_at')
            ->get()
            ->mapWithKeys(fn ($booking) => [
                $booking->id => $booking->kode_booking . ' - ' . ($booking->konsumen?->nama_lengkap ?? '-') . ' / ' . ($booking->unit?->kode_unit ?? '-'),
            ]);

        return view('marketing.pengajuan-kpr.create', compact('bookingOptions'));
    }

    public function show(int $id): View
    {
        $pengajuan = PengajuanKpr::with(['konsumen', 'booking.unit', 'booking.konsumen'])->findOrFail($id);

        if ($pengajuan->booking->id_marketing !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pengajuan KPR ini.');
        }

        return view('marketing.pengajuan-kpr.show', compact('pengajuan'));
    }

    public function edit(int $id): View
    {
        $pengajuan = PengajuanKpr::with(['booking.unit', 'booking.konsumen'])->findOrFail($id);

        if ($pengajuan->booking->id_marketing !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pengajuan KPR ini.');
        }

        if (! in_array($pengajuan->status_pengajuan, ['draft', 'ditolak'], true)) {
            abort(403, 'Pengajuan KPR hanya dapat diubah ketika masih draft atau ditolak.');
        }

        return view('marketing.pengajuan-kpr.edit', compact('pengajuan'));
    }

    public function update(PengajuanKprRequest $request, int $id): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->pengajuanKprService->updateForBooking($validated, $id);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        return redirect()->route('marketing.pengajuan-kpr.show', $id)
            ->with('success', 'Pengajuan KPR berhasil diperbarui.');
    }

    public function infoBooking(int $idBooking): JsonResponse
    {
        $booking = Booking::query()
            ->where('id_marketing', Auth::id())
            ->whereHas('statusPenjualan', function ($q) {
                $q->where('status_saat_ini', StatusPenjualan::Booking->value);
            })
            ->with(['konsumen', 'unit'])
            ->find($idBooking);

        if (! $booking) {
            return response()->json(['error' => 'Booking tidak ditemukan atau tidak tersedia.'], 404);
        }

        $missingDocuments = $this->dokumenService->getMissingDocuments($booking->id_konsumen);
        $dokumenLengkap = count($missingDocuments) === 0;

        return response()->json([
            'kode_booking' => $booking->kode_booking,
            'konsumen' => $booking->konsumen?->nama_lengkap ?? '-',
            'unit' => ($booking->unit?->kode_unit ?? '-') . ' / ' . ($booking->unit?->tipe_rumah ?? ''),
            'harga_unit_format' => 'Rp ' . number_format($booking->unit?->harga_jual ?? 0, 0, ',', '.'),
            'dokumen_lengkap' => $dokumenLengkap,
            'missing_documents' => $missingDocuments,
        ]);
    }
}
