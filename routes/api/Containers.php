<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContainerController;

Route::prefix('containers')
    ->middleware(['auth:sanctum', 'ensureUserIsActive', 'role:superadmin,moderador'])
    ->group(function () {
        Route::get('/', [ContainerController::class, 'index']);
        Route::post('/', [ContainerController::class, 'store']);
        Route::put('{id}', [ContainerController::class, 'update']);
        Route::delete('{id}', [ContainerController::class, 'destroy']);
    })
    ->where('id', '[0-9]+');
