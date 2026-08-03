<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Enums\StatusPembayaranFee;
use App\Http\Controllers\Controller;
use App\Http\Requests\PembayaranRequest;
use App\Models\Booking;
use App\Models\Pembayaran;
use App\Services\PembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PembayaranController extends Controller
{
    public function __construct(private readonly PembayaranService $pembayaranService) {}

    public function index(Request $request): View
    {
        $idMarketing = auth()->id();

        $search = $request->input('search', '');
        $filterJenis = $request->input('jenis_pembayaran', '');
        $filterStatus = $request->input('status_verifikasi', '');
        $filterBooking = $request->input('id_booking', '');
        $perPage = (int) $request->input('per_page', 10);

        $query = Pembayaran::query()
            ->whereHas('booking', function ($q) use ($idMarketing) {
                $q->where('id_marketing', $idMarketing);
            })
            ->with(['booking.konsumen', 'booking.unit']);

        if ($search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('booking', function ($q) use ($term) {
                    $q->where('kode_booking', 'like', $term)
                        ->orWhereHas('konsumen', function ($q) use ($term) {
                            $q->where('nama_lengkap', 'like', $term);
                        });
                });
            });
        }

        if ($filterJenis !== '') {
            $query->where('jenis_pembayaran', $filterJenis);
        }

        if ($filterStatus !== '') {
            $query->where('status_verifikasi', $filterStatus);
        }

        if ($filterBooking !== '') {
            $query->where('id_booking', $filterBooking);
        }

        $pembayarans = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        $stats = $this->pembayaranService->getStats($idMarketing);

        $bookingOptions = $this->pembayaranService->getActiveBookings($idMarketing)
            ->mapWithKeys(fn ($b) => [$b->id => $b->kode_booking . ' - ' . ($b->konsumen?->nama_lengkap ?? '-')]);

        return view('marketing.pembayaran.index', compact(
            'pembayarans',
            'stats',
            'search',
            'filterJenis',
            'filterStatus',
            'filterBooking',
            'perPage',
            'bookingOptions',
        ));
    }

    public function create(): View
    {
        $idMarketing = auth()->id();

        $bookingOptions = $this->pembayaranService->getActiveBookings($idMarketing)
            ->mapWithKeys(fn ($b) => [$b->id => $b->kode_booking . ' - ' . ($b->konsumen?->nama_lengkap ?? '-')]);

        return view('marketing.pembayaran.create', compact('bookingOptions'));
    }

    public function infoBooking(int $idBooking): JsonResponse
    {
        $idMarketing = auth()->id();

        $booking = Booking::query()
            ->where('id_marketing', $idMarketing)
            ->where('status_pembayaran_fee', '!=', StatusPembayaranFee::Refund->value)
            ->find($idBooking);

        if (! $booking) {
            return response()->json(['error' => 'Booking tidak ditemukan atau tidak tersedia.'], 404);
        }

        $info = $this->pembayaranService->getInfoBooking($booking->id);

        if (! $info) {
            return response()->json(['error' => 'Data booking tidak lengkap.'], 404);
        }

        return response()->json($info);
    }

    public function store(PembayaranRequest $request): RedirectResponse
    {
        try {
            $this->pembayaranService->create($request->validated());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('marketing.pembayaran.index')
            ->with('success', 'Pembayaran berhasil diinput. Status: pending hingga diverifikasi Admin.');
    }

    public function show(int $id): View
    {
        $idMarketing = auth()->id();

        $pembayaran = $this->pembayaranService->getWithRelations($id);

        if (! $pembayaran) {
            abort(404, 'Pembayaran tidak ditemukan.');
        }

        if ($pembayaran->booking->id_marketing !== $idMarketing) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pembayaran ini.');
        }

        return view('marketing.pembayaran.show', compact('pembayaran'));
    }
}
