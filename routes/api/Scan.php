<?php

use App\Http\Controllers\MaterialTypeController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

// Registro de un reciclaje: lo llama el software del contenedor (que ya
// clasificó el material), autenticado por API key — no un usuario logueado.
Route::post('scans', [ScanController::class, 'store'])->middleware('service.apiKey');

// Consulta administrativa de escaneos.
Route::middleware(['auth:sanctum', 'ensureUserIsActive', 'role:superadmin,moderador'])->group(function () {
    Route::get('scans', [ScanController::class, 'index']);
    Route::get('material-types/catalog', [MaterialTypeController::class, 'catalog']);
});
