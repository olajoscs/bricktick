<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'authenticated' => \App\Http\Middleware\EnsureAuthenticated::class,
            'api.guest' => \App\Http\Middleware\EnsureGuest::class,
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);

        $middleware->redirectGuestsTo(fn (\Illuminate\Http\Request $request) => $request->is('admin', 'admin/*')
            ? route('admin.login')
            : '/');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
