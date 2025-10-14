<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('dash')->group(function () {
        Route::get('getStats', [DashController::class, 'getStats']); // path: /api/dash/getStats
    });
});

