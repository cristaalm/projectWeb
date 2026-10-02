<?php

use App\Http\Controllers\SystemLogController;
use Illuminate\Support\Facades\Route;

// Diagnóstico del servidor. Los logs pueden traer datos sensibles (correos,
// consultas, trazas), así que solo los ve el superadmin.
Route::prefix('system')
    ->middleware(['auth:sanctum', 'ensureUserIsActive', 'role:superadmin', 'throttle:60,1'])
    ->group(function () {
        Route::get('logs', [SystemLogController::class, 'index']);
        Route::get('logs/{source}', [SystemLogController::class, 'show']);
    });
