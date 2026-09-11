<?php

namespace App\Enums;

enum RewardStatus: int
{
    case PENDING = 0;
    case APPROVED = 1;
    case REJECTED = 2;
    case PAUSED = 3;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente de revisión',
            self::APPROVED => 'Aprobada',
            self::REJECTED => 'Rechazada',
            self::PAUSED => 'Pausada',
        };
    }
}
