<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\User;

class KomisiService extends BaseService
{
    public function hitungKomisi(int $idBooking): void
    {
        $booking = Booking::query()
            ->with(['unit', 'marketing'])
            ->lockForUpdate()
            ->find($idBooking);

        if (! $booking || ! $booking->unit || ! $booking->marketing) {
            return;
        }

        $persentase = (float) ($booking->marketing->persentase_komisi ?? 0);
        $komisi = (float) $booking->booking_fee * ($persentase / 100);

        $booking->marketing->increment('total_komisi_earned', $komisi);
    }

    public function rollbackKomisi(int $idBooking): void
    {
        $booking = Booking::query()
            ->with(['unit', 'marketing'])
            ->lockForUpdate()
            ->find($idBooking);

        if (! $booking || ! $booking->unit || ! $booking->marketing) {
            return;
        }

        $persentase = (float) ($booking->marketing->persentase_komisi ?? 0);
        $komisi = (float) $booking->booking_fee * ($persentase / 100);

        $booking->marketing->decrement('total_komisi_earned', $komisi);
    }
}
