<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')
    ->middleware(['auth:sanctum', 'ensureUserIsActive', 'role:superadmin,moderador'])
    ->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('{userId}', [UserController::class, 'show']);
        Route::get('{userId}/history', [UserController::class, 'history']);
        Route::post('{userId}/points', [UserController::class, 'modifyPoints']);
        Route::post('{userId}/deactivate', [UserController::class, 'deactivate']);
        Route::post('{userId}/restore', [UserController::class, 'restore']);
        Route::post('{userId}/reset-credentials', [UserController::class, 'resetCredentials']);
        Route::post('{userId}/disable-two-factor', [UserController::class, 'disableTwoFactor']);
    })
    ->where('userId', '[0-9]+');
