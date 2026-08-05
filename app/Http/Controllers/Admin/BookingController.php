<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Konsumen;
use App\Models\Perumahan;
use App\Models\UnitRumah;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService) {}

    public function index(Request $request): View
    {
        $search = $request->input('search', '');
        $filterMarketing = $request->input('id_marketing', '');
        $filterStatusPembayaran = $request->input('status_pembayaran_fee', '');
        $filterStatusPenjualan = $request->input('status_penjualan', '');
        $filterPerumahan = $request->input('id_perumahan', '');
        $filterKategori = $request->input('kategori', '');
        $perPage = (int) $request->input('per_page', 10);

        $query = Booking::query()
            ->with(['konsumen', 'unit.perumahan', 'marketing', 'pembayaran', 'statusHistory']);

        if ($search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('kode_booking', 'like', $term)
                    ->orWhereHas('konsumen', function ($q) use ($term) {
                        $q->where('nama_lengkap', 'like', $term)
                            ->orWhere('nik', 'like', $term);
                    })
                    ->orWhereHas('unit', function ($q) use ($term) {
                        $q->where('kode_unit', 'like', $term)
                            ->orWhereHas('perumahan', function ($q) use ($term) {
                                $q->where('nama_perumahan', 'like', $term);
                            });
                    });
            });
        }

        if ($filterMarketing !== '') {
            $query->where('id_marketing', $filterMarketing);
        }

        if ($filterStatusPembayaran !== '') {
            $query->where('status_pembayaran_fee', $filterStatusPembayaran);
        }

        if ($filterStatusPenjualan !== '') {
            $query->whereHas('statusHistory', function ($q) use ($filterStatusPenjualan) {
                $q->where('status_sesudah', $filterStatusPenjualan);
            });
        }

        if ($filterPerumahan !== '') {
            $query->whereHas('unit', function ($q) use ($filterPerumahan) {
                $q->where('id_perumahan', $filterPerumahan);
            });
        }

        if ($filterKategori !== '') {
            $query->whereHas('unit', function ($q) use ($filterKategori) {
                $q->where('kategori', $filterKategori);
            });
        }

        $bookings = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        $marketingOptions = User::marketing()->aktif()->orderBy('nama_lengkap')->get()
            ->mapWithKeys(fn($m) => [$m->id => $m->nama_lengkap]);

        $perumahanOptions = Perumahan::query()
            ->where('status', 'aktif')
            ->orderBy('nama_perumahan')
            ->get()
            ->mapWithKeys(fn($p) => [$p->id => $p->nama_perumahan]);

        $stats = $this->bookingService->getStats();

        return view('admin.booking.index', array_merge(
            compact('bookings', 'search', 'filterMarketing', 'filterStatusPembayaran', 'filterStatusPenjualan', 'filterPerumahan', 'filterKategori', 'marketingOptions', 'perumahanOptions', 'stats'),
            ['perPage' => $perPage, 'hasFilters' => $search !== '' || $filterMarketing !== '' || $filterStatusPembayaran !== '' || $filterStatusPenjualan !== '' || $filterPerumahan !== '' || $filterKategori !== '']
        ));
    }

    public function show(int $id): View
    {
        $booking = $this->bookingService->getWithRelations($id);

        if (! $booking) {
            abort(404, 'Booking tidak ditemukan.');
        }

        $this->authorize('view', $booking);

        $totalTerverifikasi = $booking->pembayaran
            ->where('status_verifikasi', 'diverifikasi')
            ->sum('nominal');

        $sisaTagihan = $booking->unit->harga_jual - $totalTerverifikasi;

        return view('admin.booking.show', compact('booking', 'totalTerverifikasi', 'sisaTagihan'));
    }

    public function tracking(int $id): View
    {
        $booking = $this->bookingService->getWithRelations($id);

        if (! $booking) {
            abort(404, 'Booking tidak ditemukan.');
        }

        $this->authorize('view', $booking);

        $totalTerverifikasi = $booking->pembayaran
            ->where('status_verifikasi', 'diverifikasi')
            ->sum('nominal');

        $sisaTagihan = $booking->unit->harga_jual - $totalTerverifikasi;

        $backRoute = route('admin.booking.show', $booking->id);

        return view('booking.tracking', compact('booking', 'totalTerverifikasi', 'sisaTagihan', 'backRoute'));
    }

    public function edit(int $id): View
    {
        $booking = $this->bookingService->getWithRelations($id);

        if (! $booking) {
            abort(404, 'Booking tidak ditemukan.');
        }

        $this->authorize('update', $booking);

        $konsumenOptions = Konsumen::query()
            ->with('marketing')
            ->orderBy('nama_lengkap')
            ->get()
            ->mapWithKeys(fn($k) => [$k->id => $k->nama_lengkap . ' - ' . $k->nik . ' (' . ($k->marketing?->nama_lengkap ?? '-') . ')']);

        $unitOptions = UnitRumah::query()
            ->where('status_unit', 'tersedia')
            ->with('perumahan')
            ->orderBy('kode_unit')
            ->get()
            ->mapWithKeys(fn($u) => [$u->id => $u->kode_unit . ' - ' . $u->tipe_rumah . ' (' . $u->perumahan->nama_perumahan . ')']);

        return view('admin.booking.edit', compact('booking', 'konsumenOptions', 'unitOptions'));
    }

    public function update(BookingRequest $request, int $id): RedirectResponse
    {
        $booking = Booking::findOrFail($id);

        $this->authorize('update', $booking);

        try {
            $this->bookingService->update($request->validated(), $id);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('admin.booking.show', $id)->with('success', 'Booking berhasil diperbarui.');
    }

    public function cancel(int $id): View
    {
        $booking = $this->bookingService->getWithRelations($id);

        if (! $booking) {
            abort(404, 'Booking tidak ditemukan.');
        }

        $this->authorize('update', $booking);

        return view('admin.booking.cancel', compact('booking'));
    }

    public function processCancel(Request $request, int $id): RedirectResponse
    {
        $booking = Booking::findOrFail($id);

        $this->authorize('update', $booking);

        $request->validate([
            'alasan' => ['required', 'string', 'max:500'],
        ]);

        try {
            $this->bookingService->cancel($id, $request->input('alasan'), auth()->id());
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('admin.booking.index')->with('success', 'Booking berhasil dibatalkan.');
    }
}
