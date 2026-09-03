<?php

use App\Http\Controllers\AllianceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ensureUserIsActive'])
    ->get('alliances/catalog', [AllianceController::class, 'catalog']);

Route::prefix('alliances')
    ->middleware(['auth:sanctum', 'ensureUserIsActive', 'role:superadmin,moderador'])
    ->group(function () {
        Route::get('/', [AllianceController::class, 'index']);
        Route::post('/', [AllianceController::class, 'store']);
        Route::put('{id}', [AllianceController::class, 'update']);
        Route::delete('{id}', [AllianceController::class, 'destroy']);
        Route::post('{id}/logo', [AllianceController::class, 'uploadLogo']);
        Route::delete('{id}/logo', [AllianceController::class, 'deleteLogo']);
    })
    ->where('id', '[0-9]+');
