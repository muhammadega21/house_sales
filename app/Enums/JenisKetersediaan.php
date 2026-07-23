<?php

declare(strict_types=1);

namespace App\Enums;

enum JenisKetersediaan: string
{
    case ReadyStock = 'ready_stock';
    case Indent = 'indent';

    public function label(): string
    {
        return match($this) {
            self::ReadyStock => 'Ready Stock',
            self::Indent => 'Indent',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ReadyStock => 'success',
            self::Indent => 'warning',
        };
    }
}
