<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusUnit: string
{
    case Tersedia = 'tersedia';
    case Dibooking = 'dibooking';
    case Dijual = 'dijual';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match($this) {
            self::Tersedia => 'Tersedia',
            self::Dibooking => 'Di-booking',
            self::Dijual => 'Dijual',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Tersedia => 'success',
            self::Dibooking => 'warning',
            self::Dijual => 'primary',
            self::Dibatalkan => 'danger',
        };
    }
}
