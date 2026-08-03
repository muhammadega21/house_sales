<?php

declare(strict_types=1);

namespace App\Enums;

enum JenisPembayaran: string
{
    case BookingFee = 'booking_fee';
    case Dp = 'dp';
    case Cicilan = 'cicilan';
    case Pelunasan = 'pelunasan';

    public function label(): string
    {
        return match ($this) {
            self::BookingFee => 'Booking Fee',
            self::Dp => 'DP',
            self::Cicilan => 'Cicilan',
            self::Pelunasan => 'Pelunasan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BookingFee => 'info',
            self::Dp => 'primary',
            self::Cicilan => 'warning',
            self::Pelunasan => 'success',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::BookingFee => 'Pembayaran booking fee / uang muka booking.',
            self::Dp => 'Pembayaran uang muka (DP) unit rumah.',
            self::Cicilan => 'Pembayaran cicilan berikutnya setelah DP.',
            self::Pelunasan => 'Pembayaran pelunasan penuh harga unit.',
        };
    }
}
