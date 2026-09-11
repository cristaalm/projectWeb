<?php

use App\Http\Controllers\BadgeController;
use Illuminate\Support\Facades\Route;

// Lectura + reclamo del usuario autenticado activo — sin gate de rol,
// pensado para la futura app móvil (mismo patrón que type-shop/catalog).
Route::middleware(['auth:sanctum', 'ensureUserIsActive'])->group(function () {
    Route::get('badges/catalog', [BadgeController::class, 'catalog']);
    Route::get('badges/my-progress', [BadgeController::class, 'myProgress']);
    Route::get('badges/my-history', [BadgeController::class, 'myHistory']);
    Route::get('badges/pending-claims', [BadgeController::class, 'pendingClaims']);
    Route::post('badges/claim', [BadgeController::class, 'claim']);
});

// CRUD administrativo del catálogo de insignias.
Route::prefix('badges')
    ->middleware(['auth:sanctum', 'ensureUserIsActive', 'role:superadmin,moderador'])
    ->group(function () {
        Route::get('/', [BadgeController::class, 'index']);
        Route::post('/', [BadgeController::class, 'store']);
        Route::put('{id}', [BadgeController::class, 'update']);
        Route::delete('{id}', [BadgeController::class, 'destroy']);
    })
    ->where('id', '[0-9]+');
