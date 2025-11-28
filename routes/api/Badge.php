<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BadgeController;

Route::prefix('badges')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('getAll', [BadgeController::class, 'getAll']);
        Route::get('catalog', [BadgeController::class, 'catalogBadges']);
        
        Route::post('create', [BadgeController::class, 'create']);
        Route::post('update/{id}', [BadgeController::class, 'update']);
        Route::post('delete/{id}', [BadgeController::class, 'delete']);
        Route::post('claimBadge', [BadgeController::class, 'claimBadge']);
    });
});
