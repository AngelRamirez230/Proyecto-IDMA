<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Empleado;

class CheckDepartamento
{
    public function handle(Request $request, Closure $next, ...$departamentos)
    {
        if (!auth()->check()) {
            abort(403);
        }

        $rol = auth()->user()->idtipoDeUsuario;

        
        if ($rol == 1) {
            return $next($request);
        }

        
        if ($rol == 4) {
            return $next($request);
        }

        
        if ($rol == 2) {

            $empleado = Empleado::where(
                'idUsuario',
                auth()->user()->idUsuario
            )->first();

            if (!$empleado) {
                abort(403, 'No es empleado');
            }

            if (!in_array($empleado->idDepartamento, $departamentos)) {
                abort(403, 'Departamento no autorizado');
            }

            return $next($request);
        }

    
        abort(403, 'No autorizado');
    }
}
