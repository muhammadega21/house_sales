<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusProspek: string
{
    case Baru = 'baru';
    case Dihubungi = 'dihubungi';
    case Berminat = 'berminat';
    case TidakBerminat = 'tidak_berminat';
    case JadiKonsumen = 'jadi_konsumen';

    public function label(): string
    {
        return match ($this) {
            self::Baru => 'Baru',
            self::Dihubungi => 'Dihubungi',
            self::Berminat => 'Berminat',
            self::TidakBerminat => 'Tidak Berminat',
            self::JadiKonsumen => 'Jadi Konsumen',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Baru => 'warning',
            self::Dihubungi => 'info',
            self::Berminat => 'primary',
            self::TidakBerminat => 'danger',
            self::JadiKonsumen => 'success',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Baru => 'Prospek baru yang belum dihubungi.',
            self::Dihubungi => 'Prospek sudah dikontak dan sedang dalam proses follow-up.',
            self::Berminat => 'Prospek menunjukkan minat untuk membeli.',
            self::TidakBerminat => 'Prospek menolak atau tidak tertarik.',
            self::JadiKonsumen => 'Prospek telah berhasil dikonversi menjadi konsumen.',
        };
    }
}
