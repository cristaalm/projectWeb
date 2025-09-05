<?php

namespace App\Enums;

enum VerificationStatus: int
{
    case PENDING = 0;
    case APPROVED = 1;
    case REJECTED = 2;

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::APPROVED => 'Validado',
            self::REJECTED => 'Rechazado',
        };
    }
}
