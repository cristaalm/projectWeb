<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContainerController;

Route::prefix('containers')->group(function () {
    Route::middleware(['auth:sanctum', 'ensureUserIsActive'])->group(function () {
        Route::get('getAll', [ContainerController::class, 'getAll']);
        Route::get('catalog', [ContainerController::class, 'catalog']);
        
        Route::post('create', [ContainerController::class, 'create']);

        Route::put('update/{id}', [ContainerController::class, 'update']);
        
        Route::delete('delete/{id}', [ContainerController::class, 'delete']);
    });
    
    Route::post('update-capacity/{id}', [ContainerController::class, 'updateCapacity']);
});

