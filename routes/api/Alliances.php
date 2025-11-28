<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AllianceController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('alianzas')->group(function () {
        //GET
        Route::get('getAll', [AllianceController::class, 'getAll']);
        Route::get('catalog', [AllianceController::class, 'catalog']);
        Route::get('cashCut/{alliance_id}', [AllianceController::class, 'cashCut']);
        Route::get('stats/{alliance_id}', [AllianceController::class, 'getStatsByShop']);
        Route::get('activityByDayOfWeek/{alliance_id}', [AllianceController::class, 'getActivityByDayOfWeek']);
        Route::get('top-rewards/{alliance_id}', [AllianceController::class, 'getTopRewardsByAlliance']);

        //POST
        Route::post('create', [AllianceController::class, 'create']);
        Route::post('logo/{id}', [AllianceController::class, 'updateLogo']);
        
        //PUT
        Route::put('update/{id}', [AllianceController::class, 'update']);
        
        //DELETE
        Route::delete('delete/{id}', [AllianceController::class, 'delete']);
    });
});

