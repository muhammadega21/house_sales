<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusPernikahan: string
{
    case BelumMenikah = 'belum_menikah';
    case Menikah = 'menikah';
    case CeraiHidup = 'cerai_hidup';
    case CeraiMati = 'cerai_mati';

    public function label(): string
    {
        return match ($this) {
            self::BelumMenikah => 'Belum Menikah',
            self::Menikah => 'Menikah',
            self::CeraiHidup => 'Cerai Hidup',
            self::CeraiMati => 'Cerai Mati',
        };
    }
}