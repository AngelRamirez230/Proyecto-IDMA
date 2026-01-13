<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de asignatura</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Detalle de asignatura</h1>

        <section class="consulta-tabla-contenedor detalle-usuario">
            <div class="detalle-usuario__header">
                <div class="detalle-usuario__identidad">
                    <div class="detalle-usuario__nombre">
                        {{ $asignatura->nombre ?? 'Sin nombre' }}
                    </div>
                </div>

                <div class="detalle-usuario__acciones">
                    <a href="{{ route('consultaAsignatura') }}" class="btn-boton-formulario btn-cancelar">
                        Volver
                    </a>

                    @if(!empty($puedeEditar))
                        <a href="{{ route('asignaturas.edit', $asignatura->idAsignatura) }}" class="btn-boton-formulario">
                            Editar
                        </a>
                    @endif
                </div>
            </div>

            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Dato</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody class="tabla-cuerpo">
                    <tr>
                        <td>Nombre</td>
                        <td>{{ $asignatura->nombre ?? 'Sin nombre' }}</td>
                    </tr>
                    <tr>
                        <td>Clave</td>
                        <td>{{ $asignatura->claveAsignatura ?? 'Sin clave' }}</td>
                    </tr>
                    <tr>
                        <td>Créditos</td>
                        <td>{{ $asignatura->creditos ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Semestre</td>
                        <td>{{ $asignatura->semestre ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Horas con docente</td>
                        <td>{{ $asignatura->horasConDocente ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Horas independientes</td>
                        <td>{{ $asignatura->horasIndependientes ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Nivel de formación</td>
                        <td>{{ $asignatura->nombreNivel ?? 'Sin nivel' }}</td>
                    </tr>
                    <tr>
                        <td>Plan de estudios</td>
                        <td>{{ $asignatura->nombrePlanDeEstudios ?? 'Sin plan' }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
