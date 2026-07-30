<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Catches the case where an already-authenticated user (valid cookie session
 * or bearer token) gets deactivated mid-session — login-time checks alone
 * can't cover this, since they only run once, at login.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->trashed()) {
            return response()->json([
                'message' => 'Tu cuenta ha sido desactivada por un administrador.',
                'status' => 401,
                'authenticated' => false,
            ], 401);
        }

        return $next($request);
    }
}
