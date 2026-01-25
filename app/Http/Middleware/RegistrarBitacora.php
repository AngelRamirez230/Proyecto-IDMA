<?php

namespace App\Http\Middleware;

use App\Services\BitacoraService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrarBitacora
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!Auth::check()) {
            return $response;
        }

        if ($request->attributes->get('bitacora_registrada') === true) {
            return $response;
        }

        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        if ($request->route()?->getName() === 'logout') {
            return $response;
        }

        $tipoAccion = $this->resolverTipoAccion($request->getMethod());
        if ($tipoAccion === null) {
            return $response;
        }

        $responsableId = Auth::user()->idUsuario ?? null;
        if (!$responsableId) {
            return $response;
        }

        $afectadoId = null;
        if ($this->esAccionConUsuarioAfectado($request)) {
            $afectadoId = $request->attributes->get('bitacora_usuario_afectado')
                ?? $this->resolverIdUsuarioAfectado($request);
            if ($afectadoId !== null) {
                $afectadoId = (int) $afectadoId;
            }
        }

        $vistaDesdeRespuesta = null;
        if (method_exists($response, 'getOriginalContent')) {
            $original = $response->getOriginalContent();
            if ($original instanceof \Illuminate\View\View) {
                $vistaDesdeRespuesta = $original->getName();
            }
        }

        $nombreVista = $request->attributes->get('bitacora_nombre_vista')
            ?? $vistaDesdeRespuesta
            ?? $this->resolverNombreVista($request);

        app(BitacoraService::class)->registrar(
            $tipoAccion,
            (int) $responsableId,
            $afectadoId,
            $nombreVista
        );

        return $response;
    }

    private function resolverTipoAccion(string $method): ?int
    {
        $method = strtoupper($method);

        return match ($method) {
            'POST' => BitacoraService::ACCION_CREAR,
            'PUT', 'PATCH' => BitacoraService::ACCION_ACTUALIZAR,
            'DELETE' => BitacoraService::ACCION_ELIMINAR,
            'GET' => BitacoraService::ACCION_LEER,
            default => null,
        };
    }

    private function resolverIdUsuarioAfectado(Request $request): ?int
    {
        $usuario = $request->route('usuario');
        if (is_object($usuario) && isset($usuario->idUsuario)) {
            return (int) $usuario->idUsuario;
        }
        if (is_numeric($usuario)) {
            return (int) $usuario;
        }

        $idUsuario = $request->route('idUsuario');
        if ($idUsuario) {
            return (int) $idUsuario;
        }

        $inputIdUsuario = $request->input('idUsuarioAfectado');
        if ($inputIdUsuario) {
            return (int) $inputIdUsuario;
        }

        $route = $request->route();
        $routeName = $route?->getName();
        if ($routeName && str_starts_with($routeName, 'estudiantes.')) {
            $idEstudiante = $route->parameter('id') ?? $route->parameter('estudiante');
            if (is_numeric($idEstudiante)) {
                $estudiante = \App\Models\Estudiante::find($idEstudiante);
                if ($estudiante && $estudiante->idUsuario) {
                    return (int) $estudiante->idUsuario;
                }
            }
        }

        $path = $request->path();
        if (preg_match('#^usuarios/(\\d+)(/|$)#', (string) $path, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function resolverNombreVista(Request $request): string
    {
        $routeName = $request->route()?->getName();

        $mapa = [
            'usuarios.create' => 'shared.moduloUsuarios.altaDeUsuario',
            'usuarios.store' => 'shared.moduloUsuarios.altaDeUsuario',
            'consultaUsuarios' => 'shared.moduloUsuarios.consultaDeUsuarios',
            'usuarios.show' => 'shared.moduloUsuarios.detalleDeUsuario',
            'usuarios.edit' => 'shared.moduloUsuarios.editarDeUsuario',
            'usuarios.update' => 'shared.moduloUsuarios.editarDeUsuario',
            'usuarios.destroy' => 'shared.moduloUsuarios.consultaDeUsuarios',
            'usuarios.toggleEstatus' => 'shared.moduloUsuarios.consultaDeUsuarios',
            'apartadoBitacoras' => 'shared.moduloReportes.apartadoBitacora',
            'consultaBitacoras' => 'shared.moduloReportes.consultaDeBitacora',
        ];

        if ($routeName && isset($mapa[$routeName])) {
            return $mapa[$routeName];
        }

        return $routeName ?: $request->path();
    }

    private function esAccionConUsuarioAfectado(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return false;
        }

        if (str_starts_with($routeName, 'usuarios.') || str_starts_with($routeName, 'estudiantes.')) {
            return true;
        }

        return in_array($routeName, [
            'consultaUsuarios',
            'apartadoUsuarios',
            'consultaEstudiantes',
            'apartadoEstudiantes',
        ], true);
    }
}

