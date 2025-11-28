<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\RewardUserController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('reward')->group(function () {
        Route::get('getAll', [RewardController::class, 'getAll']); // path: /api/reward/getAll
        Route::post('create', [RewardController::class, 'create']); // path: /api/reward/create
        Route::post('update/{id}', [RewardController::class, 'update']); // path: /api/reward/update/{id}
        Route::delete('delete/{id}', [RewardController::class, 'delete']); // path: /api/reward/delete/{id}
        
        Route::post('claim', [RewardUserController::class, 'claim']); // path: /api/reward/claim
    });
});



