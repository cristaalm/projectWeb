<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Laravel\Sanctum\PersonalAccessToken;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) return $next($request);

        $token = PersonalAccessToken::findToken($bearerToken);

        if ($token && $token->expires_at && now()->greaterThan($token->expires_at)) {
            $token->delete();
            return response()->json([
                'success' => false,
                'message' => 'Su sesión ha expirado. Por favor, inicia sesión nuevamente.',
                'authenticated' => false,
                'status' => 401
            ], 401);
        }

        return $next($request);
    }
}
