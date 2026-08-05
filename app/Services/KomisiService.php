<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\MarketingTarget;
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
        $komisi = (float) $booking->unit->harga_jual * ($persentase / 100);

        $target = MarketingTarget::firstOrCreate(
            [
                'id_marketing' => $booking->marketing->id,
                'periode_bulan' => now()->month,
                'periode_tahun' => now()->year,
            ],
            ['target_unit' => 0]
        );

        $target->increment('realisasi_unit');
        $target->increment('total_nilai_penjualan', $booking->unit->harga_jual);
        $target->increment('total_komisi', $komisi);

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
        $komisi = (float) $booking->unit->harga_jual * ($persentase / 100);

        $target = MarketingTarget::query()
            ->where('id_marketing', $booking->marketing->id)
            ->where('periode_bulan', now()->month)
            ->where('periode_tahun', now()->year)
            ->first();

        if ($target) {
            $target->decrement('realisasi_unit');
            $target->decrement('total_nilai_penjualan', $booking->unit->harga_jual);
            $target->decrement('total_komisi', $komisi);
        }

        $booking->marketing->decrement('total_komisi_earned', $komisi);
    }
}
