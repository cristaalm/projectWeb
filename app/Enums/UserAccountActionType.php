<?php

namespace App\Enums;

enum UserAccountActionType: string
{
    case USER_CREATED = 'user_created';
    case DEACTIVATED = 'deactivated';
    case RESTORED = 'restored';
    case CREDENTIALS_RESET = 'credentials_reset';
    case TWO_FACTOR_DISABLED = 'two_factor_disabled';

    public function label(): string
    {
        return match ($this) {
            self::USER_CREATED => 'Cuenta creada',
            self::DEACTIVATED => 'Cuenta desactivada',
            self::RESTORED => 'Cuenta restaurada',
            self::CREDENTIALS_RESET => 'Credenciales restablecidas',
            self::TWO_FACTOR_DISABLED => '2FA deshabilitado por administrador',
        };
    }
}
