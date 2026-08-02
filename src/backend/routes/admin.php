<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');

        Route::get('project', [ProjectController::class, 'show'])->name('projects.show');
        Route::put('project', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('project', [ProjectController::class, 'destroy'])->name('projects.destroy');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::post('users', [UserController::class, 'store'])->name('users.store');

        Route::get('user', [UserController::class, 'show'])->name('users.show');
        Route::put('user', [UserController::class, 'update'])->name('users.update');
        Route::delete('user', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
