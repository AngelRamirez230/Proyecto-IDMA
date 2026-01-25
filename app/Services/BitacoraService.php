<?php

namespace App\Services;

use App\Models\Bitacora;

class BitacoraService
{
    public const ACCION_CREAR = 1;
    public const ACCION_LEER = 2;
    public const ACCION_ACTUALIZAR = 3;
    public const ACCION_ELIMINAR = 4;

    public function registrar(
        int $tipoAccion,
        int $idUsuarioResponsable,
        ?int $idUsuarioAfectado = null,
        ?string $nombreVista = null
    ): void
    {
        $payload = [
            'tipoDeAccion' => $tipoAccion,
            'idUsuarioResponsable' => $idUsuarioResponsable,
            'idUsuarioAfectado' => $idUsuarioAfectado,
            'nombreVista' => $nombreVista,
        ];

        try {
            Bitacora::create($payload);
        } catch (\Throwable $e) {
            report($e);

            if ($idUsuarioAfectado === null) {
                try {
                    $payload['idUsuarioAfectado'] = $idUsuarioResponsable;
                    Bitacora::create($payload);
                } catch (\Throwable $retryException) {
                    report($retryException);
                }
            }
        }
    }
}
