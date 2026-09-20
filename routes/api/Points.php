<?php

use App\Http\Controllers\PointController;
use Illuminate\Support\Facades\Route;

// Lectura de los puntos del usuario autenticado activo — sin gate de rol,
// pensado para la app móvil (mismo patrón que badges/my-progress).
Route::prefix('points')
    ->middleware(['auth:sanctum', 'ensureUserIsActive'])
    ->group(function () {
        Route::get('balance', [PointController::class, 'balance']);
        Route::get('movements', [PointController::class, 'movements']);
        Route::get('month', [PointController::class, 'month']);
    });
