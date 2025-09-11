<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']); // path: /api/auth/login
    Route::post('passHash', [AuthController::class, 'passHash']); // path: /api/auth/passHash
    Route::post('validateToken', [AuthController::class, 'validateToken']); // path: /api/auth/validateToken
    Route::post('logout', [AuthController::class, 'logout']); // path: /api/auth/logout
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']); // path: /api/auth/forgot-password
    Route::post('reset-password', [AuthController::class, 'resetPassword']); // path: /api/auth/reset-password
});

