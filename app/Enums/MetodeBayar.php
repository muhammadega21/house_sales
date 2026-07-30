<?php

declare(strict_types=1);

namespace App\Enums;

enum MetodeBayar: string
{
    case Transfer = 'transfer';
    case Tunai = 'tunai';
    case Debit = 'debit';
    case Kpr = 'kpr';

    public function label(): string
    {
        return match ($this) {
            self::Transfer => 'Transfer',
            self::Tunai => 'Tunai',
            self::Debit => 'Debit',
            self::Kpr => 'KPR',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Transfer => '🏦',
            self::Tunai => '💵',
            self::Debit => '💳',
            self::Kpr => '🏠',
        };
    }
}