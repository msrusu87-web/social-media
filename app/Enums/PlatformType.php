<?php

namespace App\Enums;

enum PlatformType: string
{
    case FACEBOOK = 'facebook';
    case INSTAGRAM = 'instagram';
    case X = 'x';
    case TIKTOK = 'tiktok';
    case YOUTUBE = 'youtube';
    case PINTEREST = 'pinterest';

    public function label(): string
    {
        return match($this) {
            self::FACEBOOK => 'Facebook',
            self::INSTAGRAM => 'Instagram',
            self::X => 'X (Twitter)',
            self::TIKTOK => 'TikTok',
            self::YOUTUBE => 'YouTube',
            self::PINTEREST => 'Pinterest',
        };
    }
}
