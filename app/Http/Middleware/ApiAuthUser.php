<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class ApiAuthUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $next($request);
        }

        $personalAccessToken = PersonalAccessToken::findToken($token);

        if (!$personalAccessToken) {
            return response()->json([
                'message' => 'Token inválido',
                'status' => 401,
                'authenticated' => false
            ], 401);
        }

        $user = $personalAccessToken->tokenable;

        if (!$user || !$user->status) {
            return response()->json([
                'message' => 'Tu cuenta ha sido desactivada por un administrador',
                'status' => 401,
                'authenticated' => false
            ], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
