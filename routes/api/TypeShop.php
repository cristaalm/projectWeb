<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TypeShopController;

Route::middleware(['auth:sanctum', 'ensureUserIsActive'])->group(function () {
    Route::prefix('typeShop')->group(function () {
        Route::get('getAll', [TypeShopController::class, 'getAll']);
        Route::get('catalog', [TypeShopController::class, 'catalog']);
        Route::post('create', [TypeShopController::class, 'create']);
        Route::delete('delete/{id}', [TypeShopController::class, 'delete']);
    });
});

