<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusPembayaranFee: string
{
    case BelumBayar = 'belum_bayar';
    case SudahBayar = 'sudah_bayar';
    case Refund = 'refund';

    public function label(): string
    {
        return match ($this) {
            self::BelumBayar => 'Belum Bayar',
            self::SudahBayar => 'Sudah Bayar',
            self::Refund => 'Refund',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BelumBayar => 'warning',
            self::SudahBayar => 'success',
            self::Refund => 'danger',
        };
    }
}