<?php

declare(strict_types=1);

namespace App\Enums;

enum SumberProspek: string
{
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case Tiktok = 'tiktok';
    case WalkIn = 'walk_in';
    case Referral = 'referral';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::Facebook => 'Facebook',
            self::Instagram => 'Instagram',
            self::Tiktok => 'TikTok',
            self::WalkIn => 'Walk In',
            self::Referral => 'Referral',
            self::Lainnya => 'Lainnya',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Facebook => '📘',
            self::Instagram => '📸',
            self::Tiktok => '🎵',
            self::WalkIn => '🚶',
            self::Referral => '🤝',
            self::Lainnya => '📝',
        };
    }
}
