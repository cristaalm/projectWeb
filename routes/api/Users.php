<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IdentifyVerificationController;

Route::prefix('users')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {

        // Controller user
        Route::get('getAll', [UserController::class, 'getAll']);
        Route::post('toggleStatusAccount', [UserController::class, 'toggleStatusAccount']);
        
        // Controller IdentifyVerification
        Route::post('uploadDocuments', [IdentifyVerificationController::class, 'uploadDocuments']);
        Route::post('uploadSelfie', [IdentifyVerificationController::class, 'uploadSelfie']);
        Route::get('documents/{type}/{userId}', [IdentifyVerificationController::class, 'getDocument'])
        ->where('type', 'front|back|selfie');
        Route::get('list-docs', [IdentifyVerificationController::class, 'getListDocs']);
        Route::post('verification-user', [IdentifyVerificationController::class, 'verificationUser']);
    });
    
    // Controller user
    Route::post('identityUserCode', [UserController::class, 'identityUserCode']);
    Route::post('identityUser', [UserController::class, 'identityUser']);
    Route::post('register', [UserController::class, 'registerUser']);
});

