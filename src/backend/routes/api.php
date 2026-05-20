<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/auth/session', [AuthController::class, 'guestSession']);
});

Route::middleware('authenticated')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::get('/dashboard', [DashboardController::class, 'show']);
});
