<?php

declare(strict_types=1);

namespace App\Enums;

enum JenisDokumen: string
{
    case Ktp = 'ktp';
    case Kk = 'kk';
    case Npwp = 'npwp';
    case SlipGaji = 'slip_gaji';
    case RekeningKoran = 'rekening_koran';
    case SuratKerja = 'surat_kerja';
    case SuratNikah = 'surat_nikah';
    case SuratKeteranganPenghasilan = 'surat_keterangan_penghasilan';
    case FormulirKpr = 'formulir_kpr';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match($this) {
            self::Ktp => 'KTP',
            self::Kk => 'Kartu Keluarga',
            self::Npwp => 'NPWP',
            self::SlipGaji => 'Slip Gaji',
            self::RekeningKoran => 'Rekening Koran',
            self::SuratKerja => 'Surat Kerja',
            self::SuratNikah => 'Surat Nikah',
            self::SuratKeteranganPenghasilan => 'Surat Keterangan Penghasilan',
            self::FormulirKpr => 'Formulir KPR',
            self::Lainnya => 'Lainnya',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Ktp, self::Kk, self::Npwp => 'primary',
            self::SlipGaji, self::RekeningKoran, self::SuratKeteranganPenghasilan => 'success',
            self::SuratKerja, self::SuratNikah, self::FormulirKpr => 'info',
            self::Lainnya => 'secondary',
        };
    }
}
