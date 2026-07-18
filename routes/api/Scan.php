<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;

Route::prefix('scans')->group(function () {
    Route::post('scan', [ScanController::class, 'scan']);

    Route::middleware(['auth:sanctum', 'ensureUserIsActive'])->group(function () {
        Route::get('total-type-scans', [ScanController::class, 'totalTypeScans']);
    });
});
