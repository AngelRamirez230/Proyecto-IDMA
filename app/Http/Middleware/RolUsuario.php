<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolUsuario
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('idTipoDeUsuario')) {
            return redirect()->route('login.form');
        }

        if (session('idTipoDeUsuario') != $rol) {
            abort(403, 'No autorizado');
        }

        return $next($request);
    
    }
}
