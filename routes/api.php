<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/api/Auth.php';
require __DIR__ . '/api/TypeShop.php';
require __DIR__ . '/api/Alliances.php';
require __DIR__ . '/api/Scan.php';
require __DIR__ . '/api/Rewards.php';
require __DIR__ . '/api/History.php';
require __DIR__ . '/api/Containers.php';
require __DIR__ . '/api/Users.php';
require __DIR__ . '/api/Dash.php';
require __DIR__ . '/api/Notifications.php';
require __DIR__ . '/api/Badge.php';

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'time' => now()]);
});
