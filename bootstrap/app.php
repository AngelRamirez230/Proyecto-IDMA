<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    using: function () {
        // RUTAS WEB (necesitan middleware web)
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        // RUTAS API (sin sesiones)
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));
    },
    commands: base_path('routes/console.php'),
    health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
