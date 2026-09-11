<?php

use App\Http\Controllers\RedemptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('redemptions')
    ->middleware(['auth:sanctum', 'ensureUserIsActive', 'role:superadmin,moderador,admin_merchant'])
    ->group(function () {
        Route::get('/', [RedemptionController::class, 'index']);
        Route::post('{id}/deliver', [RedemptionController::class, 'deliver']);
    })
    ->where('id', '[0-9]+');
