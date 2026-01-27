<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Bitacoras del sistema</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Bitácoras del sistema</h1>

        <section class="consulta-tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Fecha</th>
                        <th>Acción</th>
                        <th>Responsable</th>
                        <th>Usuario afectado</th>
                        <th>Vista</th>
                    </tr>
                </thead>

                <tbody class="tabla-cuerpo">
                    @if ($bitacoras->isEmpty())
                        <tr>
                            <td colspan="5" class="tablaVacia">
                                No existen registros en la bitácora.
                            </td>
                        </tr>
                    @else
                        @foreach ($bitacoras as $bitacora)
                            @php
                                $responsable = $bitacora->usuarioResponsable;
                                $afectado = $bitacora->usuarioAfectado;

                                $nombreResponsable = $responsable
                                    ? trim(
                                        ($responsable->primerNombre ?? '') . ' ' .
                                        ($responsable->segundoNombre ?? '') . ' ' .
                                        ($responsable->primerApellido ?? '') . ' ' .
                                        ($responsable->segundoApellido ?? '')
                                    )
                                    : 'Sin responsable';

                                $nombreAfectado = $afectado
                                    ? trim(
                                        ($afectado->primerNombre ?? '') . ' ' .
                                        ($afectado->segundoNombre ?? '') . ' ' .
                                        ($afectado->primerApellido ?? '') . ' ' .
                                        ($afectado->segundoApellido ?? '')
                                    )
                                    : 'Sin afectado';

                                $vista = $bitacora->nombreVista ?? '';
                                $esVistaUsuario = str_starts_with($vista, 'shared.moduloUsuarios.')
                                    || str_starts_with($vista, 'shared.moduloEstudiantes.');
                                $mostrarAfectado = $afectado
                                    && ($esVistaUsuario || ((int) $bitacora->idUsuarioAfectado !== (int) $bitacora->idUsuarioResponsable));
                            @endphp

                            <tr class="tabla-fila">
                                <td>{{ $bitacora->fecha ?? 'Sin fecha' }}</td>
                                <td>{{ $bitacora->accion_nombre }}</td>
                                <td>{{ $nombreResponsable ?: 'Sin responsable' }}</td>
                                <td>{{ $mostrarAfectado ? ($nombreAfectado ?: '') : '' }}</td>
                                <td>{{ $bitacora->nombreVista ?? 'Sin vista' }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </section>

        <div class="paginacion">
            {!! $bitacoras->links() !!}
        </div>
    </main>
</body>
</html>
