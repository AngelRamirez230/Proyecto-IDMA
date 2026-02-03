<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de calificaciones</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Lista de calificaciones</h1>

        <section class="consulta-controles">
            <form action="{{ route('consultaCalificaciones') }}" method="GET">
                <div class="consulta-busqueda-group">
                    <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                    <input
                        type="text"
                        id="buscarHorario"
                        name="buscarHorario"
                        placeholder="Ingresa asignatura, grupo, docente o aula"
                        value="{{ $buscar ?? '' }}"
                        onkeydown="if(event.key === 'Enter') this.form.submit();"
                    >
                </div>

                <input type="hidden" name="orden" value="{{ $orden ?? '' }}">
                <input type="hidden" name="dia" value="{{ $dia ?? '' }}">
            </form>

            <div class="consulta-selects">
                <form action="{{ route('consultaCalificaciones') }}" method="GET" id="formFiltroCalificaciones">
                    <input type="hidden" name="buscarHorario" value="{{ $buscar ?? '' }}">

                    <select name="dia" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled {{ empty($dia) ? 'selected' : '' }}>Filtrar por día</option>
                        <option value="">Ver todos</option>
                        @foreach ($dias as $diaItem)
                            <option value="{{ $diaItem->idDiaSemana }}" {{ (string)($dia ?? '') === (string)$diaItem->idDiaSemana ? 'selected' : '' }}>
                                {{ $diaItem->nombreDia }}
                            </option>
                        @endforeach
                    </select>

                    <select name="orden" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled {{ empty($orden) ? 'selected' : '' }}>Ordenar por</option>
                        <option value="asignatura_az" {{ ($orden ?? '') === 'asignatura_az' ? 'selected' : '' }}>Asignatura A-Z</option>
                        <option value="asignatura_za" {{ ($orden ?? '') === 'asignatura_za' ? 'selected' : '' }}>Asignatura Z-A</option>
                        <option value="grupo_az" {{ ($orden ?? '') === 'grupo_az' ? 'selected' : '' }}>Grupo A-Z</option>
                        <option value="grupo_za" {{ ($orden ?? '') === 'grupo_za' ? 'selected' : '' }}>Grupo Z-A</option>
                        <option value="docente_az" {{ ($orden ?? '') === 'docente_az' ? 'selected' : '' }}>Docente A-Z</option>
                        <option value="docente_za" {{ ($orden ?? '') === 'docente_za' ? 'selected' : '' }}>Docente Z-A</option>
                        <option value="aula_az" {{ ($orden ?? '') === 'aula_az' ? 'selected' : '' }}>Aula A-Z</option>
                        <option value="aula_za" {{ ($orden ?? '') === 'aula_za' ? 'selected' : '' }}>Aula Z-A</option>
                        <option value="hora_asc" {{ ($orden ?? '') === 'hora_asc' ? 'selected' : '' }}>Hora menor a mayor</option>
                    </select>
                </form>
            </div>
        </section>

        <section class="consulta-tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Asignatura</th>
                        <th>Grupo</th>
                        <th>Docente</th>
                        <th>Bloque</th>
                        <th>Aula</th>
                        <th>Dia</th>
                        <th>Hora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="tabla-cuerpo">
                    @if ($horarios->isEmpty())
                        <tr>
                            <td colspan="8" class="tablaVacia">
                                @if(!empty($buscar))
                                    No se encontraron coincidencias con la busqueda realizada.
                                @else
                                    No existen horarios registrados.
                                @endif
                            </td>
                        </tr>
                    @else
                        @foreach ($horarios as $horario)
                            <tr class="tabla-fila">
                                <td>{{ $horario->asignatura ?? 'Sin asignatura' }}</td>
                                <td>{{ $horario->claveGrupo ?? 'Sin grupo' }}</td>
                                <td>{{ $horario->docente ?: 'Sin docente' }}</td>
                                <td>{{ $horario->numeroBloque ?? '-' }}</td>
                                <td>{{ $horario->aula ?? 'Sin aula' }}</td>
                                <td>{{ $horario->dia ?? 'Sin dia' }}</td>
                                <td>
                                    @if(!empty($horario->horaInicio) && !empty($horario->horaFin))
                                        {{ $horario->horaInicio }} - {{ $horario->horaFin }}
                                    @else
                                        Sin hora
                                    @endif
                                </td>
                                <td>
                                    <div class="tabla-acciones">
                                        <a href="{{ route('calificaciones.edit', $horario->idHorario) }}"
                                            class="accion-boton"
                                            title="Editar">
                                            <img src="{{ asset('imagenes/IconoEditar.png') }}" alt="Editar">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </section>

        <div class="paginacion">
            {!! $horarios->links() !!}
        </div>
    </main>
</body>
</html>
