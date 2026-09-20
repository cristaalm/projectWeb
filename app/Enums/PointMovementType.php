<?php

namespace App\Enums;

enum PointMovementType: string
{
    case EARNING = 'earning';
    case BADGE = 'badge';
    case ADJUSTMENT = 'adjustment';
    case REDEMPTION = 'redemption';

    public function label(): string
    {
        return match ($this) {
            self::EARNING => 'Reciclaje',
            self::BADGE => 'Insignia',
            self::ADJUSTMENT => 'Ajuste',
            self::REDEMPTION => 'Canje',
        };
    }
}
