<?php

use App\Http\Controllers\ContainerController;
use Illuminate\Support\Facades\Route;

Route::prefix('containers')
    ->middleware(['auth:sanctum', 'ensureUserIsActive', 'role:superadmin,moderador'])
    ->group(function () {
        Route::get('/', [ContainerController::class, 'index']);
        Route::post('/', [ContainerController::class, 'store']);
        Route::put('{id}', [ContainerController::class, 'update']);
        Route::delete('{id}', [ContainerController::class, 'destroy']);
        Route::get('{id}/token', [ContainerController::class, 'token']);
        Route::post('{id}/token/regenerate', [ContainerController::class, 'regenerateToken']);
    })
    ->where('id', '[0-9]+');
