<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\KategoriRumah;
use App\Enums\StatusPembayaranFee;
use App\Enums\StatusPenjualan;
use App\Enums\StatusUnit;
use App\Models\Booking;
use App\Models\StatusHistory;
use App\Models\UnitRumah;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService extends BaseService
{
    public function create(array $data, Model|string|null $model = null): Booking
    {
        return DB::transaction(function () use ($data): Booking {
            $unit = UnitRumah::lockForUpdate()->findOrFail($data['id_unit']);

            if ($unit->status_unit !== StatusUnit::Tersedia) {
                throw ValidationException::withMessages([
                    'id_unit' => 'Unit tidak tersedia untuk dibooking. Status unit: '.$unit->status_unit->label(),
                ]);
            }

            $kategori = $unit->kategori;
            $minFee = $kategori === KategoriRumah::Subsidi
                ? 1000000
                : 5000000;

            if ($data['booking_fee'] < $minFee) {
                throw ValidationException::withMessages([
                    'booking_fee' => "Booking fee minimum untuk kategori {$kategori->label()} adalah Rp ".number_format($minFee, 0, ',', '.'),
                ]);
            }

            $kodeBooking = $this->generateKodeBooking();

            $booking = parent::create([
                'kode_booking' => $kodeBooking,
                'id_konsumen' => $data['id_konsumen'],
                'id_unit' => $data['id_unit'],
                'id_marketing' => $data['id_marketing'] ?? Auth::id(),
                'tanggal_booking' => $data['tanggal_booking'],
                'booking_fee' => $data['booking_fee'],
                'status_pembayaran_fee' => StatusPembayaranFee::BelumBayar->value,
                'metode_bayar_fee' => $data['metode_bayar_fee'] ?? null,
                'bukti_bayar_fee' => null,
                'catatan' => $data['catatan'] ?? null,
            ], Booking::class);

            // BookingObserver::created handles: unit → dibooking, StatusPenjualan, StatusHistory

            if (isset($data['bukti_bayar_fee']) && $data['bukti_bayar_fee']) {
                $path = $this->uploadFile($data['bukti_bayar_fee'], 'bukti-bayar', null);
                $booking->update([
                    'bukti_bayar_fee' => $path,
                    'status_pembayaran_fee' => StatusPembayaranFee::SudahBayar->value,
                    'tanggal_bayar_fee' => now()->toDateString(),
                    'metode_bayar_fee' => $data['metode_bayar_fee'] ?? null,
                ]);
            }

            return $booking->fresh(['konsumen', 'unit', 'marketing']);
        });
    }

    public function generateKodeBooking(): string
    {
        $today = now()->format('Ymd');
        $prefix = "BK-{$today}-";

        $lastBooking = Booking::query()
            ->where('kode_booking', 'like', "{$prefix}%")
            ->orderByDesc('kode_booking')
            ->first();

        if ($lastBooking) {
            $lastNumber = (int) substr($lastBooking->kode_booking, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix.str_pad((string) $newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function update(array $data, Model|string|int $model, string|int|null $id = null): Booking
    {
        $id ??= $model;

        $booking = $this->findById(Booking::class, $id, ['konsumen', 'unit', 'statusHistory']);

        $statusPenjualan = $booking->statusHistory()
            ->where('status_sesudah', '!=', StatusPenjualan::Batal->value)
            ->where('status_sesudah', '!=', StatusPenjualan::Prospek->value)
            ->orderByDesc('id')
            ->first();

        $currentStatus = $statusPenjualan?->status_sesudah ?? StatusPenjualan::Booking->value;

        if ($currentStatus !== StatusPenjualan::Booking->value) {
            throw ValidationException::withMessages([
                'status' => 'Booking hanya bisa diubah jika status penjualan masih \'booking\'.',
            ]);
        }

        if (isset($data['bukti_bayar_fee']) && $data['bukti_bayar_fee']) {
            $oldFile = $booking->bukti_bayar_fee;
            $path = $this->uploadFile($data['bukti_bayar_fee'], 'bukti-bayar', $oldFile);
            $data['bukti_bayar_fee'] = $path;
            $data['status_pembayaran_fee'] = StatusPembayaranFee::SudahBayar->value;
            $data['tanggal_bayar_fee'] = now()->toDateString();
        }

        $booking->update($data);

        return $booking->fresh();
    }

    public function cancel(int $idBooking, string $alasan, int $userId): void
    {
        DB::transaction(function () use ($idBooking, $alasan, $userId): void {
            $booking = Booking::findOrFail($idBooking);

            $statusPenjualan = \App\Models\StatusPenjualan::where('id_booking', $idBooking)->first();

            if (! $statusPenjualan) {
                throw ValidationException::withMessages([
                    'status' => 'Status penjualan tidak ditemukan untuk booking ini.',
                ]);
            }

            $currentStatus = $statusPenjualan->status_saat_ini instanceof StatusPenjualan
                ? $statusPenjualan->status_saat_ini
                : StatusPenjualan::tryFrom((string) $statusPenjualan->status_saat_ini) ?? StatusPenjualan::Booking;

            if ($currentStatus === StatusPenjualan::SerahTerima) {
                throw ValidationException::withMessages([
                    'status' => 'Booking tidak bisa dibatalkan setelah serah terima.',
                ]);
            }

            app(StatusPenjualanService::class)->transition(
                $statusPenjualan,
                StatusPenjualan::Batal->value,
                $alasan,
                $userId,
            );

            if ($booking->status_pembayaran_fee === StatusPembayaranFee::SudahBayar) {
                $booking->update([
                    'status_pembayaran_fee' => StatusPembayaranFee::Refund->value,
                ]);
            }
        });
    }

    public function getForMarketing(int $idMarketing): Collection
    {
        return Booking::query()
            ->where('id_marketing', $idMarketing)
            ->with(['konsumen', 'unit', 'pembayaran', 'statusHistory', 'statusPenjualan'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getWithRelations(int $id): ?Booking
    {
        return Booking::query()
            ->with([
                'konsumen',
                'unit.perumahan',
                'marketing',
                'pembayaran' => fn ($q) => $q->orderBy('tanggal_bayar', 'desc'),
                'statusPenjualan',
                'statusHistory' => fn ($q) => $q->orderBy('created_at', 'asc'),
            ])
            ->find($id);
    }

    public function getStats(): array
    {
        $bulanIni = now()->startOfMonth();

        return [
            'total_aktif' => Booking::query()
                ->whereHas('statusHistory', function ($q) {
                    $q->where('status_sesudah', StatusPenjualan::Booking->value);
                })->count(),
            'bulan_ini' => Booking::query()
                ->where('tanggal_booking', '>=', $bulanIni)
                ->count(),
            'total_booking_fee' => Booking::query()
                ->where('status_pembayaran_fee', StatusPembayaranFee::SudahBayar->value)
                ->sum('booking_fee'),
        ];
    }
}
