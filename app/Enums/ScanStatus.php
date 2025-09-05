<?php

namespace App\Enums;

enum ScanStatus: int
{
    case SUCCESS = 1;
    case FAILED = 0;

    public function label(): string
    {
        return match($this) {
            self::SUCCESS => 'Éxito',
            self::FAILED => 'Fallo',
        };
    }
}
