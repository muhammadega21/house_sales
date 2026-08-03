<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusVerifikasiDokumen: string
{
    case BelumDiverifikasi = 'belum_diverifikasi';
    case Valid = 'valid';
    case TidakValid = 'tidak_valid';
    case PerluRevisi = 'perlu_revisi';

    public function label(): string
    {
        return match ($this) {
            self::BelumDiverifikasi => 'Belum Diverifikasi',
            self::Valid => 'Valid',
            self::TidakValid => 'Tidak Valid',
            self::PerluRevisi => 'Perlu Revisi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BelumDiverifikasi => 'warning',
            self::Valid => 'success',
            self::TidakValid => 'danger',
            self::PerluRevisi => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::BelumDiverifikasi => '⏳',
            self::Valid => '✅',
            self::TidakValid => '❌',
            self::PerluRevisi => '⚠️',
        };
    }
}
