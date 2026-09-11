<?php

use App\Http\Controllers\RewardController;
use Illuminate\Support\Facades\Route;

Route::prefix('rewards')
    ->middleware(['auth:sanctum', 'ensureUserIsActive', 'role:superadmin,moderador,admin_merchant'])
    ->group(function () {
        Route::get('/', [RewardController::class, 'index']);
        Route::post('/', [RewardController::class, 'store']);
        Route::put('{id}', [RewardController::class, 'update']);
        Route::delete('{id}', [RewardController::class, 'destroy']);
        Route::post('{id}/image', [RewardController::class, 'uploadImage']);
        Route::delete('{id}/image', [RewardController::class, 'deleteImage']);
        Route::post('{id}/approve', [RewardController::class, 'approve'])->middleware('role:superadmin,moderador');
        Route::post('{id}/reject', [RewardController::class, 'reject'])->middleware('role:superadmin,moderador');
        Route::post('{id}/pause', [RewardController::class, 'pause']);
        Route::post('{id}/reactivate', [RewardController::class, 'reactivate']);
    })
    ->where('id', '[0-9]+');
