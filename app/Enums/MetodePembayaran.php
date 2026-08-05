<?php

declare(strict_types=1);

namespace App\Enums;

enum MetodePembayaran: string
{
    case Kpr = 'kpr';
    case CashBertahap = 'cash_bertahap';
    case CashKeras = 'cash_keras';

    public function label(): string
    {
        return match ($this) {
            self::Kpr => 'KPR',
            self::CashBertahap => 'Cash Bertahap',
            self::CashKeras => 'Cash Keras',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Kpr => 'Pembayaran dengan cicilan KPR menggunakan bunga anuitas.',
            self::CashBertahap => 'Pembayaran tanpa bunga, cicilan dibayar bertahap sesuai tenor.',
            self::CashKeras => 'Pembayaran lunas sekaligus dengan diskon jika tersedia.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Kpr => 'banknotes',
            self::CashBertahap => 'clock',
            self::CashKeras => 'sparkles',
        };
    }

    public function butuhTenor(): bool
    {
        return in_array($this, [self::Kpr, self::CashBertahap], true);
    }

    public function butuhBunga(): bool
    {
        return $this === self::Kpr;
    }
}
