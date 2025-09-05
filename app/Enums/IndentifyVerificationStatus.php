<?php

namespace App\Enums;

enum IndentifyVerificationStatus: int
{
    case PENDING = 0;
    case APPROVED = 1;
    case REJECTED = 2;

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        };
    }
}
