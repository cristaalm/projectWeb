<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IdentifyVerificationController;

Route::prefix('users')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {

        // Controller user
        Route::get('getAll', [UserController::class, 'getAll']);
        Route::post('toggleStatusAccount', [UserController::class, 'toggleStatusAccount']);
        Route::post('modifyPoints', [UserController::class, 'modifyPoints']);
        Route::post('create', [UserController::class, 'create']);
        Route::post('tourComplete/{userId}', [UserController::class, 'tourComplete']);
        Route::post('updateField/{field}/{userId}', [UserController::class, 'updateField'])
        ->where('field', 'name|last_name|email|phone|curp');
        
        // Controller IdentifyVerification
        Route::post('verification-user', [IdentifyVerificationController::class, 'verificationUser']);
        Route::post('uploadDocuments', [IdentifyVerificationController::class, 'uploadDocuments']);
        Route::post('uploadSelfie', [IdentifyVerificationController::class, 'uploadSelfie']);
        Route::post('toggle-status-pending/{userId}', [IdentifyVerificationController::class, 'toggleStatusPending']);
        Route::post('documents/{type}/{userId}', [IdentifyVerificationController::class, 'uploadDoc'])
        ->where('type', 'ine_front|ine_back|selfie');
        Route::get('list-docs', [IdentifyVerificationController::class, 'getListDocs']);
        Route::get('documents/{type}/{userId}', [IdentifyVerificationController::class, 'getDocument'])
        ->where('type', 'front|back|selfie');
    });
    
    // Controller user
    Route::post('identityUserCode', [UserController::class, 'identityUserCode']);
    Route::post('identityUser', [UserController::class, 'identityUser']);
    Route::post('register', [UserController::class, 'registerUser']);
});

