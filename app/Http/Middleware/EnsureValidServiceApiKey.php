<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autenticación máquina-a-máquina para endpoints llamados por servicios internos
 * (ej. el backend del asistente Evi), no por un usuario logueado con Sanctum.
 */
class EnsureValidServiceApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.evi.api_key');
        $provided = $request->header('X-Api-Key');

        if (! $expected || ! $provided || ! hash_equals($expected, $provided)) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado.',
                'data' => null,
                'errors' => null,
                'code' => 401,
            ], 401);
        }

        return $next($request);
    }
}
