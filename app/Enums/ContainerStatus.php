<?php

namespace App\Enums;

enum ContainerStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Activo',
            self::INACTIVE => 'Inactivo',
        };
    }
}
