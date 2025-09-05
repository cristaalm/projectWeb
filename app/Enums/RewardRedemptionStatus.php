<?php

namespace App\Enums;

enum RewardRedemptionStatus: int
{
    case REDEEMED = 1;
    case DELIVERED = 2;
    case CANCELLED = 3;
    case EXPIRED = 4;

    public function label(): string
    {
        return match($this) {
            self::REDEEMED => 'Canjeado',
            self::DELIVERED => 'Entregado',
            self::CANCELLED => 'Cancelado',
            self::EXPIRED => 'Expirado',
        };
    }
}
