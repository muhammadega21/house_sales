<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\StatusPenjualan as StatusPenjualanEnum;
use App\Enums\StatusUnit;
use App\Models\Booking;
use App\Models\StatusHistory;
use App\Models\StatusPenjualan;
use App\Models\UnitRumah;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        UnitRumah::where('id', $booking->id_unit)
            ->update(['status_unit' => StatusUnit::Dibooking->value]);

        StatusPenjualan::create([
            'id_booking' => $booking->id,
            'id_konsumen' => $booking->id_konsumen,
            'id_unit' => $booking->id_unit,
            'status_saat_ini' => StatusPenjualanEnum::Booking->value,
            'tanggal_perubahan' => now(),
            'diubah_oleh' => $booking->id_marketing,
            'catatan' => 'Booking dibuat',
        ]);

        StatusHistory::create([
            'id_booking' => $booking->id,
            'status_sebelum' => null,
            'status_sesudah' => StatusPenjualanEnum::Booking->value,
            'catatan' => 'Booking dibuat',
            'diubah_oleh' => $booking->id_marketing,
        ]);

        activity_log([
            'id_user' => $booking->id_marketing,
            'aksi' => 'create',
            'entitas' => 'booking',
            'entitas_id' => $booking->id,
            'deskripsi' => 'Booking dibuat dan status penjualan diinisialisasi ke Booking.',
            'data_baru' => [
                'kode_booking' => $booking->kode_booking,
                'status_saat_ini' => StatusPenjualanEnum::Booking->value,
            ],
        ]);
    }
}
