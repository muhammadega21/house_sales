<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusPenjualan: string
{
    case Prospek = 'prospek';
    case Booking = 'booking';
    case PengajuanKpr = 'pengajuan_kpr';
    case Akad = 'akad';
    case SerahTerima = 'serah_terima';
    case Batal = 'batal';

    public function label(): string
    {
        return match($this) {
            self::Prospek => 'Prospek',
            self::Booking => 'Booking',
            self::PengajuanKpr => 'Pengajuan KPR',
            self::Akad => 'Akad',
            self::SerahTerima => 'Serah Terima',
            self::Batal => 'Batal',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Prospek => 'info',
            self::Booking => 'warning',
            self::PengajuanKpr => 'primary',
            self::Akad => 'success',
            self::SerahTerima => 'dark',
            self::Batal => 'danger',
        };
    }
}
