<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de grupo</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Detalle de grupo</h1>

        <section class="consulta-tabla-contenedor detalle-usuario">
            <div class="detalle-usuario__header">
                <div class="detalle-usuario__identidad">
                    <div class="detalle-usuario__nombre">
                        {{ $grupo->claveGrupo ?? 'Sin clave' }}
                    </div>
                </div>

                <div class="detalle-usuario__acciones">
                    <a href="{{ route('consultaGrupo') }}" class="btn-boton-formulario btn-cancelar">
                        Volver
                    </a>

                    @if(auth()->check() && (int)auth()->user()->idtipoDeUsuario === 1)
                        <a href="{{ route('grupos.edit', $grupo->idGrupo) }}" class="btn-boton-formulario">
                            Editar
                        </a>
                    @endif
                </div>
            </div>

            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Campo</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody class="tabla-cuerpo">
                    <tr>
                        <td>Clave</td>
                        <td>{{ $grupo->claveGrupo ?? 'Sin clave' }}</td>
                    </tr>
                    <tr>
                        <td>Licenciatura</td>
                        <td>{{ $grupo->nombreLicenciatura ?? 'Sin licenciatura' }}</td>
                    </tr>
                    <tr>
                        <td>Semestre</td>
                        <td>{{ $grupo->semestre ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Modalidad</td>
                        <td>{{ $grupo->nombreModalidad ?? 'Sin modalidad' }}</td>
                    </tr>
                    <tr>
                        <td>Periodo academico</td>
                        <td>{{ $grupo->nombreCicloEscolar ?? 'Sin periodo' }}</td>
                    </tr>
                    <tr>
                        <td>Estatus</td>
                        <td>{{ ((int)($grupo->idEstatus ?? 0) === 2) ? 'Suspendido' : 'Activo' }}</td>
                    </tr>
                    <tr>
                        <td>Inscritos</td>
                        <td>{{ $inscritos ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="consulta-tabla-contenedor">
            <h2 class="consulta-titulo consulta-subtitulo">Estudiantes del grupo</h2>

            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Matrícula</th>
                        <th>Nombre completo</th>
                        <th>Generación</th>
                    </tr>
                </thead>
                <tbody class="tabla-cuerpo">
                    @if ($estudiantes->isEmpty())
                        <tr>
                            <td colspan="3" class="tablaVacia">
                                No hay estudiantes inscritos a este grupo.
                            </td>
                        </tr>
                    @else
                        @foreach ($estudiantes as $estudiante)
                            @php
                                $nombreCompleto = trim(
                                    ($estudiante->primerNombre ?? '') . ' ' .
                                    ($estudiante->segundoNombre ?? '') . ' ' .
                                    ($estudiante->primerApellido ?? '') . ' ' .
                                    ($estudiante->segundoApellido ?? '')
                                );
                            @endphp
                            <tr class="tabla-fila">
                                <td>{{ $estudiante->matriculaAlfanumerica ?? 'Sin matricula' }}</td>
                                <td>{{ $nombreCompleto ?: 'Sin nombre' }}</td>
                                <td>{{ $estudiante->idGeneracion ?? 'N/D' }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
