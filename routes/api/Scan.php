<?php

use App\Http\Controllers\MaterialTypeController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

// Endpoints del contenedor físico, autenticado por su propio token (header
// X-Container-Token) — no un usuario logueado. El token solo abre estas dos
// rutas, y el contenedor siempre opera a su propio nombre.
Route::prefix('scans')->middleware('container.token')->group(function () {
    Route::post('identify', [ScanController::class, 'identify']);
    Route::post('/', [ScanController::class, 'store']);
});

// Consulta administrativa de escaneos.
Route::middleware(['auth:sanctum', 'ensureUserIsActive', 'role:superadmin,moderador'])->group(function () {
    Route::get('scans', [ScanController::class, 'index']);
    Route::get('material-types/catalog', [MaterialTypeController::class, 'catalog']);
});
