<?php

namespace App\Enums;

enum VerificationStatus: int
{
    case PENDING = 0;
    case APPROVED = 1;
    case REJECTED = 2;
    case EMPTY = 3;

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::APPROVED => 'Validado',
            self::REJECTED => 'Rechazado',
            self::EMPTY => 'Vacio',
        };
    }
}
