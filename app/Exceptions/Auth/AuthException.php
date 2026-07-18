<?php

namespace App\Exceptions\Auth;

use Exception;

class AuthException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $status = 422,
        public readonly ?string $details = null,
    ) {
        parent::__construct($message);
    }
}
