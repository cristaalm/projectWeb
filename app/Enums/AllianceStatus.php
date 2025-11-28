<?php

namespace App\Enums;

enum AllianceStatus: int
{
    case ACTIVE = 1;
    case PAUSED = 0;

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Activo',
            self::PAUSED => 'Pausado',
        };
    }
}
