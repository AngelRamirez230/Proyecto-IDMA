<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TipoUsuarioMiddleware
{
    public function handle(Request $request, Closure $next, $tipo)
    {
        if (
            !auth()->check() ||
            auth()->user()->idtipoDeUsuario != $tipo
        ) {
            abort(403);
        }

        return $next($request);
    }
}
