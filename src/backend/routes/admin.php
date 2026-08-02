<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ProjectController;
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
    });
});
