<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    protected $timeout = 600;

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('lastActivityTime')) {
            $inactive = time() - $request->session()->get('lastActivityTime');

            if ($inactive > $this->timeout) {
                // Limpiar sesión
                $request->session()->flush();

                // Redirigir a login con mensaje
                return redirect()->route('login.form')
                                 ->with('timeout', 'Su sesión ha expirado por inactividad.');
            }
        }

        // Guardar el timestamp de la última actividad
        $request->session()->put('lastActivityTime', time());

        return $next($request);
    }
}
