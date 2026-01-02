<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class AuthManual
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        if (!Auth::check() && session()->has('idUsuario')) {
            $usuario = Usuario::find(session('idUsuario'));

            if ($usuario) {
                Auth::login($usuario);
            }
        }

        
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        return $next($request);
    }
}
