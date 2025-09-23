<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AllianceController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('alianzas')->group(function () {
        Route::get('getAll', [AllianceController::class, 'getAll']);
        Route::post('create', [AllianceController::class, 'create']);
        Route::put('update/{id}', [AllianceController::class, 'update']);
        Route::delete('delete/{id}', [AllianceController::class, 'delete']);
        Route::post('logo/{id}', [AllianceController::class, 'updateLogo']);
    });
});

