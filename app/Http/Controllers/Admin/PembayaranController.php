<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifikasiPembayaranRequest;
use App\Models\Pembayaran;
use App\Models\User;
use App\Services\PembayaranService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PembayaranController extends Controller
{
    public function __construct(private readonly PembayaranService $pembayaranService) {}

    public function index(Request $request): View
    {
        $search = $request->input('search', '');
        $filterStatus = $request->input('status_verifikasi', '');
        $filterJenis = $request->input('jenis_pembayaran', '');
        $filterMarketing = $request->input('id_marketing', '');
        $filterTanggalFrom = $request->input('tanggal_from', '');
        $filterTanggalTo = $request->input('tanggal_to', '');
        $perPage = (int) $request->input('per_page', 10);

        $query = Pembayaran::query()
            ->with(['booking.konsumen', 'booking.unit', 'booking.marketing']);

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

        if ($filterStatus !== '') {
            $query->where('status_verifikasi', $filterStatus);
        }

        if ($filterJenis !== '') {
            $query->where('jenis_pembayaran', $filterJenis);
        }

        if ($filterMarketing !== '') {
            $query->whereHas('booking', function ($q) use ($filterMarketing) {
                $q->where('id_marketing', $filterMarketing);
            });
        }

        if ($filterTanggalFrom !== '' && $filterTanggalTo !== '') {
            $query->whereBetween('tanggal_bayar', [$filterTanggalFrom, $filterTanggalTo]);
        } elseif ($filterTanggalFrom !== '') {
            $query->where('tanggal_bayar', '>=', $filterTanggalFrom);
        } elseif ($filterTanggalTo !== '') {
            $query->where('tanggal_bayar', '<=', $filterTanggalTo);
        }

        $pembayarans = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        $stats = $this->pembayaranService->getStatsForAdmin();

        $marketingOptions = User::marketing()->aktif()->orderBy('nama_lengkap')->get()
            ->mapWithKeys(fn ($m) => [$m->id => $m->nama_lengkap]);

        return view('admin.pembayaran.index', compact(
            'pembayarans',
            'stats',
            'search',
            'filterStatus',
            'filterJenis',
            'filterMarketing',
            'filterTanggalFrom',
            'filterTanggalTo',
            'perPage',
            'marketingOptions',
        ));
    }

    public function show(int $id): View
    {
        $pembayaran = $this->pembayaranService->getWithRelations($id);

        if (! $pembayaran) {
            abort(404, 'Pembayaran tidak ditemukan.');
        }

        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function verifikasi(int $id): View
    {
        $pembayaran = $this->pembayaranService->getWithRelations($id);

        if (! $pembayaran) {
            abort(404, 'Pembayaran tidak ditemukan.');
        }

        if ($pembayaran->status_verifikasi !== \App\Enums\StatusVerifikasi::Pending) {
            return redirect()->route('admin.pembayaran.show', $id)
                ->with('warning', 'Pembayaran ini sudah diproses.');
        }

        $unit = $pembayaran->booking?->unit;
        $hargaUnit = $unit ? (float) $unit->harga_jual : 0;
        $dpMinPersen = $unit ? (float) $unit->dp_minimum_persen : 0;
        $dpMinNominal = $hargaUnit * ($dpMinPersen / 100);
        $totalTerverifikasi = $this->pembayaranService->getTotalTerverifikasi($pembayaran->id_booking);
        $sisaTagihan = $hargaUnit - $totalTerverifikasi;

        $expectedNominal = match ($pembayaran->jenis_pembayaran) {
            \App\Enums\JenisPembayaran::BookingFee => (float) ($pembayaran->booking?->booking_fee ?? 0),
            \App\Enums\JenisPembayaran::Dp => $dpMinNominal,
            \App\Enums\JenisPembayaran::Pelunasan => $sisaTagihan,
            default => 0,
        };

        return view('admin.pembayaran.verifikasi', compact(
            'pembayaran', 'hargaUnit', 'dpMinPersen', 'dpMinNominal', 'expectedNominal', 'sisaTagihan'
        ));
    }

    public function prosesVerifikasi(VerifikasiPembayaranRequest $request, int $id): RedirectResponse
    {
        $pembayaran = Pembayaran::findOrFail($id);

        if ($pembayaran->status_verifikasi !== \App\Enums\StatusVerifikasi::Pending) {
            return redirect()->route('admin.pembayaran.show', $id)
                ->with('warning', 'Pembayaran ini sudah diproses.');
        }

        try {
            $this->pembayaranService->verifikasi(
                $id,
                $request->input('status_verifikasi'),
                $request->input('catatan_verifikasi'),
                auth()->id(),
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.pembayaran.verifikasi', $id)
                ->withErrors($e->errors());
        }

        $statusLabel = $request->input('status_verifikasi') === \App\Enums\StatusVerifikasi::Diverifikasi->value
            ? 'disetujui' : 'ditolak';

        return redirect()->route('admin.pembayaran.show', $id)
            ->with('success', 'Pembayaran berhasil ' . $statusLabel . '.');
    }

    public function tolak(int $id): View
    {
        $pembayaran = $this->pembayaranService->getWithRelations($id);

        if (! $pembayaran) {
            abort(404, 'Pembayaran tidak ditemukan.');
        }

        if ($pembayaran->status_verifikasi !== \App\Enums\StatusVerifikasi::Pending) {
            return redirect()->route('admin.pembayaran.show', $id)
                ->with('warning', 'Pembayaran ini sudah diproses.');
        }

        return view('admin.pembayaran.tolak', compact('pembayaran'));
    }

    public function prosesTolak(Request $request, int $id): RedirectResponse
    {
        $pembayaran = Pembayaran::findOrFail($id);

        if ($pembayaran->status_verifikasi !== \App\Enums\StatusVerifikasi::Pending) {
            return redirect()->route('admin.pembayaran.show', $id)
                ->with('warning', 'Pembayaran ini sudah diproses.');
        }

        $request->validate([
            'catatan_verifikasi' => ['required', 'string', 'max:500'],
        ]);

        try {
            $this->pembayaranService->verifikasi(
                $id,
                \App\Enums\StatusVerifikasi::Ditolak->value,
                $request->input('catatan_verifikasi'),
                auth()->id(),
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.pembayaran.tolak', $id)
                ->withErrors($e->errors());
        }

        return redirect()->route('admin.pembayaran.show', $id)
            ->with('success', 'Pembayaran berhasil ditolak.');
    }

    public function batchVerifikasi(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'exists:pembayaran,id'],
            'status_verifikasi' => ['required', 'in:diverifikasi,ditolak'],
            'catatan_verifikasi' => ['nullable', 'string', 'max:500'],
        ]);

        $ids = $request->input('ids');
        $status = $request->input('status_verifikasi');
        $catatan = $request->input('catatan_verifikasi');
        $adminId = auth()->id();

        if ($status === \App\Enums\StatusVerifikasi::Ditolak->value && empty($catatan)) {
            return redirect()->back()->with('error', 'Catatan wajib diisi saat menolak.');
        }

        $success = 0;
        $failed = 0;

        foreach ($ids as $id) {
            try {
                $this->pembayaranService->verifikasi(
                    $id,
                    $status,
                    $catatan,
                    $adminId,
                );
                $success++;
            } catch (\Throwable $e) {
                $failed++;
            }
        }

        return redirect()->route('admin.pembayaran.index')
            ->with('success', "Berhasil memverifikasi {$success} pembayaran" . ($failed > 0 ? ", {$failed} gagal." : '.'));
    }
}
