<?php

use App\Http\Controllers\AvatarController;
use Illuminate\Support\Facades\Route;

Route::prefix('avatar')->middleware('service.apiKey')->group(function () {
    Route::post('identify', [AvatarController::class, 'identify']);

    Route::prefix('{user}')->where(['user' => '[0-9]+'])->group(function () {
        Route::post('memories', [AvatarController::class, 'storeMemory']);
        Route::post('feedback', [AvatarController::class, 'storeFeedback']);
        Route::patch('state', [AvatarController::class, 'updateState']);
    });
});
