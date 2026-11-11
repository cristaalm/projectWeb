<?php

use App\Http\Controllers\NearbyController;
use Illuminate\Support\Facades\Route;

// "Cerca de ti" para la app móvil — sin gate de rol, mismo patrón que points/*.
// POST a propósito: la ubicación va en el cuerpo y no queda en los logs de acceso.
Route::prefix('nearby')
    ->middleware(['auth:sanctum', 'ensureUserIsActive'])
    ->group(function () {
        Route::post('rewards', [NearbyController::class, 'rewards']);
        Route::post('shops', [NearbyController::class, 'shops']);
    });
