<?php

use App\Http\Controllers\AllianceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ensureUserIsActive'])
    ->get('alliances/catalog', [AllianceController::class, 'catalog']);
