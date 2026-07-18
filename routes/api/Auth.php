<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']); // path: /api/auth/login
    Route::post('register', [AuthController::class, 'register']); // path: /api/auth/register
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']); // path: /api/auth/forgot-password
    Route::post('reset-password', [AuthController::class, 'resetPassword']); // path: /api/auth/reset-password

    Route::middleware(['auth:sanctum', 'ensureUserIsActive'])->group(function () {
        Route::get('me', [AuthController::class, 'me']); // path: /api/auth/me
        Route::post('logout', [AuthController::class, 'logout']); // path: /api/auth/logout
        Route::get('generateQR2FA', [AuthController::class, 'generateQR2FA']); // path: /api/auth/generateQR2FA
        Route::post('enable-2fa', [AuthController::class, 'enable2FA']); // path: /api/auth/enable-2fa
        Route::post('verify-2fa', [AuthController::class, 'verify2FA']); // path: /api/auth/verify-2fa
        Route::post('disable-2fa', [AuthController::class, 'disable2FA']); // path: /api/auth/disable-2fa
    });
});
