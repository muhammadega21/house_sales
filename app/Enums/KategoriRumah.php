<?php

declare(strict_types=1);

namespace App\Enums;

enum KategoriRumah: string
{
    case Subsidi = 'subsidi';
    case NonSubsidi = 'non_subsidi';

    public function label(): string
    {
        return match($this) {
            self::Subsidi => 'Subsidi',
            self::NonSubsidi => 'Non-Subsidi',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Subsidi => 'success',
            self::NonSubsidi => 'primary',
        };
    }
}
