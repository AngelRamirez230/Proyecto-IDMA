<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;

use Carbon\Carbon;

Carbon::setLocale('es');


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // ADMINISTRADOR = 1
        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->idtipoDeUsuario === 1;
        });

        // ESTUDIANTE = 4
        Blade::if('estudiante', function () {
            return auth()->check() && auth()->user()->idtipoDeUsuario === 4;
        });
    }
}
