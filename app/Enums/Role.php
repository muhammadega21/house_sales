<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Marketing = 'marketing';
    case Manajemen = 'manajemen';

    public function label(): string
    {
        return match($this) {
            self::Admin => 'Admin',
            self::Marketing => 'Marketing',
            self::Manajemen => 'Manajemen',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Admin => 'danger',
            self::Marketing => 'primary',
            self::Manajemen => 'success',
        };
    }
}
