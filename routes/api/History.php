<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HistoryController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('history')->group(function () {
        Route::get('getAll', [HistoryController::class, 'getAll']); // path: /api/history/getAll
        Route::get('getAllSystem', [HistoryController::class, 'getAllSystem']);

        Route::get('topAlliancesByRedemptions', [HistoryController::class, 'getTopAlliancesByRedemptions']);

    });
});



