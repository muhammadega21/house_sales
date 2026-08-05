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
        return match ($this) {
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
        return match ($this) {
            self::Prospek => 'gray',
            self::Booking => 'amber',
            self::PengajuanKpr => 'indigo',
            self::Akad => 'blue',
            self::SerahTerima => 'emerald',
            self::Batal => 'red',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Prospek => 'user-group',
            self::Booking => 'bookmark',
            self::PengajuanKpr => 'document-text',
            self::Akad => 'handshake',
            self::SerahTerima => 'home',
            self::Batal => 'x-circle',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Prospek => 'Calon konsumen belum melakukan booking.',
            self::Booking => 'Booking fee sudah dibayar dan unit dikunci sementara.',
            self::PengajuanKpr => 'Dokumen diajukan ke bank untuk proses KPR.',
            self::Akad => 'KPR disetujui dan akad ditandatangani.',
            self::SerahTerima => 'Rumah diserahkan ke konsumen.',
            self::Batal => 'Transaksi dibatalkan dan unit dikembalikan.',
        };
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Prospek => [self::Booking, self::Batal],
            self::Booking => [self::PengajuanKpr, self::Batal],
            self::PengajuanKpr => [self::Akad, self::Batal],
            self::Akad => [self::SerahTerima, self::Batal],
            self::SerahTerima => [],
            self::Batal => [],
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::SerahTerima, self::Batal], true);
    }
}
