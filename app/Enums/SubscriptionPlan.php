<?php

namespace App\Enums;

enum SubscriptionPlan: string
{
    case FREE = 'free';
    case BASIC = 'basic';
    case PRO = 'pro';
    case ENTERPRISE = 'enterprise';

    public function label(): string
    {
        return match($this) {
            self::FREE => 'Free',
            self::BASIC => 'Basic',
            self::PRO => 'Pro',
            self::ENTERPRISE => 'Enterprise',
        };
    }

    public function monthlyPostLimit(): int
    {
        return match($this) {
            self::FREE => 10,
            self::BASIC => 50,
            self::PRO => 200,
            self::ENTERPRISE => -1, // unlimited
        };
    }

    public function price(): float
    {
        return match($this) {
            self::FREE => 0,
            self::BASIC => 9.99,
            self::PRO => 29.99,
            self::ENTERPRISE => 99.99,
        };
    }
}
