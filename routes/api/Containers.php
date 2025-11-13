<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContainerController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('containers')->group(function () {
        Route::get('getAll', [ContainerController::class, 'getAll']);
        Route::get('catalog', [ContainerController::class, 'catalog']);
        
        Route::post('create', [ContainerController::class, 'create']);
        Route::post('update-capacity/{id}', [ContainerController::class, 'updateCapacity']);

        Route::put('update/{id}', [ContainerController::class, 'update']);
        
        Route::delete('delete/{id}', [ContainerController::class, 'delete']);

    });
});

