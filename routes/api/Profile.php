<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')->middleware(['auth:sanctum', 'ensureUserIsActive'])->group(function () {
    Route::put('/', [ProfileController::class, 'update']); // path: /api/profile
    Route::post('avatar', [ProfileController::class, 'updateAvatar']); // path: /api/profile/avatar
    Route::delete('avatar', [ProfileController::class, 'deleteAvatar']); // path: /api/profile/avatar
    Route::post('email', [ProfileController::class, 'updateEmail']); // path: /api/profile/email
    Route::post('password', [ProfileController::class, 'updatePassword']); // path: /api/profile/password
    Route::post('social', [ProfileController::class, 'linkSocialAccount']); // path: /api/profile/social
    Route::post('social/unlink', [ProfileController::class, 'unlinkSocialAccount']); // path: /api/profile/social/unlink
});
