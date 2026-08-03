<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Enums\KategoriRumah;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Konsumen;
use App\Models\UnitRumah;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService) {}

    public function index(Request $request): View
    {
        $idMarketing = auth()->id();
        $search = $request->input('search', '');
        $filterStatusPembayaran = $request->input('status_pembayaran_fee', '');
        $filterStatusPenjualan = $request->input('status_penjualan', '');
        $perPage = (int) $request->input('per_page', 10);

        $query = Booking::query()
            ->where('id_marketing', $idMarketing)
            ->with(['konsumen', 'unit', 'pembayaran', 'statusHistory']);

        if ($search !== '') {
            $term = '%'.$search.'%';
            $query->where(function ($q) use ($term) {
                $q->where('kode_booking', 'like', $term)
                    ->orWhereHas('konsumen', function ($q) use ($term) {
                        $q->where('nama_lengkap', 'like', $term)
                            ->orWhere('nik', 'like', $term);
                    });
            });
        }

        if ($filterStatusPembayaran !== '') {
            $query->where('status_pembayaran_fee', $filterStatusPembayaran);
        }

        if ($filterStatusPenjualan !== '') {
            $query->whereHas('statusHistory', function ($q) use ($filterStatusPenjualan) {
                $q->where('status_sesudah', $filterStatusPenjualan);
            });
        }

        $bookings = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        $stats = $this->bookingService->getStats();

        return view('marketing.booking.index', array_merge(
            compact('bookings', 'search', 'filterStatusPembayaran', 'filterStatusPenjualan', 'stats'),
            ['perPage' => $perPage, 'hasFilters' => $search !== '' || $filterStatusPembayaran !== '' || $filterStatusPenjualan !== '']
        ));
    }

    public function create(): View
    {
        $konsumenOptions = Konsumen::query()
            ->where('id_marketing', auth()->id())
            ->orderBy('nama_lengkap')
            ->get()
            ->mapWithKeys(fn ($k) => [$k->id => $k->nama_lengkap.' - '.$k->nik]);

        $unitOptions = UnitRumah::query()
            ->where('status_unit', 'tersedia')
            ->with('perumahan')
            ->orderBy('kode_unit')
            ->get()
            ->mapWithKeys(fn ($u) => [$u->id => $u->kode_unit.' - '.$u->tipe_rumah.' ('.$u->perumahan->nama_perumahan.')']);

        return view('marketing.booking.create', compact('konsumenOptions', 'unitOptions'));
    }

    public function cekUnit(int $idUnit): JsonResponse
    {
        $unit = UnitRumah::with('perumahan')->find($idUnit);

        if (! $unit) {
            return response()->json(['available' => false, 'message' => 'Unit tidak ditemukan'], 404);
        }

        return response()->json([
            'available' => $unit->status_unit === 'tersedia',
            'kode_unit' => $unit->kode_unit,
            'tipe_rumah' => $unit->tipe_rumah,
            'kategori' => $unit->kategori->value,
            'jenis_ketersediaan' => $unit->jenis_ketersediaan->value,
            'luas_tanah' => $unit->luas_tanah,
            'luas_bangunan' => $unit->luas_bangunan,
            'harga_jual' => $unit->harga_jual,
            'harga_jual_format' => 'Rp '.number_format($unit->harga_jual, 0, ',', '.'),
            'booking_fee_min' => $unit->kategori === KategoriRumah::Subsidi ? 1000000 : 5000000,
            'perumahan' => $unit->perumahan->nama_perumahan,
            'foto_unit' => $unit->foto_unit ? asset('storage/'.$unit->foto_unit) : null,
        ]);
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $validated['id_marketing'] = auth()->id();
            $booking = $this->bookingService->create($validated);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('marketing.booking.show', $booking->id)->with('success', 'Booking berhasil dibuat dengan kode '.$booking->kode_booking);
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

        return view('marketing.booking.show', compact('booking', 'totalTerverifikasi', 'sisaTagihan'));
    }

    public function edit(int $id): View
    {
        $booking = $this->bookingService->getWithRelations($id);

        if (! $booking) {
            abort(404, 'Booking tidak ditemukan.');
        }

        $this->authorize('update', $booking);

        $konsumenOptions = Konsumen::query()
            ->where('id_marketing', auth()->id())
            ->orderBy('nama_lengkap')
            ->get()
            ->mapWithKeys(fn ($k) => [$k->id => $k->nama_lengkap.' - '.$k->nik]);

        $unitOptions = UnitRumah::query()
            ->where('status_unit', 'tersedia')
            ->with('perumahan')
            ->orderBy('kode_unit')
            ->get()
            ->mapWithKeys(fn ($u) => [$u->id => $u->kode_unit.' - '.$u->tipe_rumah.' ('.$u->perumahan->nama_perumahan.')']);

        return view('marketing.booking.edit', compact('booking', 'konsumenOptions', 'unitOptions'));
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

        return redirect()->route('marketing.booking.show', $id)->with('success', 'Booking berhasil diperbarui.');
    }

    public function cancel(int $id): View
    {
        $booking = $this->bookingService->getWithRelations($id);

        if (! $booking) {
            abort(404, 'Booking tidak ditemukan.');
        }

        $this->authorize('update', $booking);

        return view('marketing.booking.cancel', compact('booking'));
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

        return redirect()->route('marketing.booking.index')->with('success', 'Booking berhasil dibatalkan.');
    }
}
