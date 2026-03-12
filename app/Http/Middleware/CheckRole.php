<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            abort(403, 'No autenticado');
        }

        if (!in_array(auth()->user()->idtipoDeUsuario, $roles)) {
            abort(403, 'Usuario no autorizado');
        }

        return $next($request);
    }
}
