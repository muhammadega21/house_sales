<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusVerifikasi: string
{
    case Pending = 'pending';
    case Diverifikasi = 'diverifikasi';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Pending',
            self::Diverifikasi => 'Diverifikasi',
            self::Ditolak => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::Diverifikasi => 'success',
            self::Ditolak => 'danger',
        };
    }
}
