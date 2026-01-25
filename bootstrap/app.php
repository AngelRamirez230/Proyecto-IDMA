<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AuthManual;
use App\Http\Middleware\RolUsuario;
use App\Http\Middleware\RedirectManual;
use App\Http\Middleware\NoCache;
use App\Http\Middleware\SessionTimeout;
use App\Http\Middleware\RegistrarBitacora;


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
        $middleware->alias([
            'auth.manual' => AuthManual::class,
            'rol' => RolUsuario::class,
            'guest.manual' => RedirectManual::class,
            'nocache'      => NoCache::class,
            'activity.timeout'  => SessionTimeout::class,
            'tipoUsuario' => \App\Http\Middleware\TipoUsuarioMiddleware::class,
            'bitacora' => RegistrarBitacora::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
