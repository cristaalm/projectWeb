<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Panel principal. Cada rol con acceso al dashboard web tiene su propio
// endpoint, porque los datos que ve cada uno son distintos.
Route::prefix('dashboard')->middleware(['auth:sanctum', 'ensureUserIsActive'])->group(function () {
    Route::get('superadmin', [DashboardController::class, 'superadmin'])->middleware('role:superadmin');
});
