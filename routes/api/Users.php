<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('getAll', [UserController::class, 'getAll']);
        Route::post('toggleStatusAccount', [UserController::class, 'toggleStatusAccount']);
    });
});

