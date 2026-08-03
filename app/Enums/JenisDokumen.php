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

    public function wajib(): bool
    {
        return match($this) {
            self::Ktp, self::Kk, self::Npwp, self::SlipGaji, self::RekeningKoran, self::SuratKerja, self::FormulirKpr => true,
            self::SuratNikah, self::SuratKeteranganPenghasilan, self::Lainnya => false,
        };
    }

    public function maxsize(): int
    {
        return match($this) {
            self::SlipGaji, self::RekeningKoran, self::Lainnya => 10240,
            self::Ktp, self::Kk, self::Npwp, self::SuratKerja, self::SuratNikah, self::SuratKeteranganPenghasilan, self::FormulirKpr => 5120,
        };
    }

    public function keterangan(): string
    {
        return match($this) {
            self::Ktp => 'Salinan KTP asli yang masih berlaku, pastikan jelas dan terbaca.',
            self::Kk => 'Salinan Kartu Keluarga yang masih berlaku.',
            self::Npwp => 'Nomor Pokok Wajib Pajak (NPWP) yang masih berlaku.',
            self::SlipGaji => 'Slip gaji 3 bulan terakhir dari perusahaan.',
            self::RekeningKoran => 'Rekening koran 3 bulan terakhir dari bank.',
            self::SuratKerja => 'Surat keterangan kerja dari perusahaan/tempat kerja.',
            self::SuratNikah => 'Salinan akte nikah atau surat keterangan nikah.',
            self::SuratKeteranganPenghasilan => 'Surat keterangan penghasilan dari pihak yang berwenang.',
            self::FormulirKpr => 'Formulir permohonan KPR dari bank yang dituju.',
            self::Lainnya => 'Dokumen pendukung tambahan sesuai kebutuhan.',
        };
    }
}
