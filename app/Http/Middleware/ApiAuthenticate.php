<?php

// ======================== IMPORTANTE ========================
/**
 * Este middleware sobrescribe el predeterminado 'auth' porque:
 * - El original redirige a /login en web, lo cual rompe APIs.
 * - Queremos respuestas JSON consistentes en errores 401.
 * - Se maneja en conjunto con ApiAuthUser y CheckTokenExpiration.
 */

// Modificado por: Eduardo Arcega Rodriguez

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class ApiAuthenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}
