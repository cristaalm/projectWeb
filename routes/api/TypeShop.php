<?php

use App\Http\Controllers\TypeShopController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ensureUserIsActive'])
    ->get('type-shop/catalog', [TypeShopController::class, 'catalog']);

Route::prefix('type-shop')
    ->middleware(['auth:sanctum', 'ensureUserIsActive', 'role:superadmin,moderador'])
    ->group(function () {
        Route::get('/', [TypeShopController::class, 'index']);
        Route::post('/', [TypeShopController::class, 'store']);
        Route::put('{id}', [TypeShopController::class, 'update']);
        Route::delete('{id}', [TypeShopController::class, 'destroy']);
    })
    ->where('id', '[0-9]+');
