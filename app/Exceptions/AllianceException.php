<?php

namespace App\Exceptions;

use Exception;

class AllianceException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $status = 422,
        public readonly ?string $details = null,
    ) {
        parent::__construct($message);
    }
}
