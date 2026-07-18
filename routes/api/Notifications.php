<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationsController;

Route::prefix('notifications')->group(function () {
    Route::middleware(['auth:sanctum', 'ensureUserIsActive'])->group(function () {
        Route::post('registerToken', [NotificationsController::class, 'registerToken']); // POST /api/notifications/registerToken
        Route::delete('registerToken', [NotificationsController::class, 'deleteToken']); // DELETE /api/notifications/registerToken
        Route::post('send', [NotificationsController::class, 'send']); // POST /api/notifications/send
    });
});