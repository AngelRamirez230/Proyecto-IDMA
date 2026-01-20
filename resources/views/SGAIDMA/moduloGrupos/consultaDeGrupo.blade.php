<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Consulta de grupos</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Lista de grupos</h1>

        <section class="consulta-controles">
            <form action="{{ route('consultaGrupo') }}" method="GET">
                <div class="consulta-busqueda-group">
                    <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                    <input
                        type="text"
                        id="buscarGrupo"
                        name="buscarGrupo"
                        placeholder="Ingresa clave, licenciatura, semestre o periodo"
                        value="{{ $buscar ?? '' }}"
                        onkeydown="if(event.key === 'Enter') this.form.submit();"
                    >
                </div>

                <input type="hidden" name="filtro" value="{{ $filtro ?? '' }}">
                <input type="hidden" name="orden" value="{{ $orden ?? '' }}">
            </form>

            <div class="consulta-selects">
                <form action="{{ route('consultaGrupo') }}" method="GET" id="formFiltroGrupos">
                    <input type="hidden" name="buscarGrupo" value="{{ $buscar ?? '' }}">

                    @if(auth()->check() && (int)auth()->user()->idtipoDeUsuario === 1)
                        <select name="filtro" class="select select-boton" onchange="this.form.submit()">
                            <option value="" disabled {{ empty($filtro) ? 'selected' : '' }}>Filtrar por</option>
                            <option value="activos" {{ ($filtro ?? '') === 'activos' ? 'selected' : '' }}>Activos</option>
                            <option value="suspendidos" {{ ($filtro ?? '') === 'suspendidos' ? 'selected' : '' }}>Suspendidos</option>
                        </select>
                    @endif

                    <select name="orden" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled {{ empty($orden) ? 'selected' : '' }}>Ordenar por</option>
                        <option value="clave" {{ ($orden ?? '') === 'clave' ? 'selected' : '' }}>Clave A-Z</option>
                        <option value="semestre" {{ ($orden ?? '') === 'semestre' ? 'selected' : '' }}>Semestre</option>
                        <option value="periodo_antiguo" {{ ($orden ?? '') === 'periodo_antiguo' ? 'selected' : '' }}>Periodo mas antiguo</option>
                        <option value="periodo_reciente" {{ ($orden ?? '') === 'periodo_reciente' ? 'selected' : '' }}>Periodo mas reciente</option>
                    </select>
                </form>
            </div>
        </section>

        <section class="consulta-tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Clave</th>
                        <th>Licenciatura</th>
                        <th>Semestre</th>
                        <th>Modalidad</th>
                        <th>Inscritos</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="tabla-cuerpo">
                    @if ($grupos->isEmpty())
                        <tr>
                            <td colspan="7" class="tablaVacia">
                                @if(!empty($buscar))
                                    No se encontraron coincidencias con la busqueda realizada.
                                @else
                                    No existen grupos registrados.
                                @endif
                            </td>
                        </tr>
                    @else
                        @foreach ($grupos as $grupo)
                            @php
                                $esSuspendido = ((int)($grupo->idEstatus ?? 0) === 2);
                            @endphp
                            <tr class="tabla-fila {{ $esSuspendido ? 'fila-suspendida' : '' }}">
                                <td>{{ $grupo->claveGrupo ?? 'Sin clave' }}</td>
                                <td>{{ $grupo->nombreLicenciatura ?? 'Sin licenciatura' }}</td>
                                <td>{{ $grupo->semestre ?? '-' }}</td>
                                <td>{{ $grupo->nombreModalidad ?? 'Sin modalidad' }}</td>
                                <td>{{ $grupo->inscritos ?? 0 }}</td>
                                <td>
                                    {{ $esSuspendido ? 'Suspendido' : 'Activo' }}
                                </td>
                                <td>
                                    <div class="tabla-acciones">
                                        <a href="{{ route('grupos.show', $grupo->idGrupo) }}"
                                            class="accion-boton"
                                            title="Ver detalles">
                                            <img
                                                src="{{ $esSuspendido
                                                    ? asset('imagenes/IconoDetallesAsignaturaGris.png')
                                                    : asset('imagenes/IconoDetallesAsignatura.png') }}"
                                                alt="Ver"
                                            >
                                        </a>

                                        @if(auth()->check() && (int)auth()->user()->idtipoDeUsuario === 1)
                                            <a href="{{ route('grupos.edit', $grupo->idGrupo) }}"
                                                class="accion-boton"
                                                title="Editar">
                                                <img
                                                    src="{{ $esSuspendido
                                                        ? asset('imagenes/IconoEditarGris.png')
                                                        : asset('imagenes/IconoEditar.png') }}"
                                                    alt="Editar"
                                                >
                                            </a>

                                            <form action="{{ route('grupos.toggleEstatus', $grupo->idGrupo) }}" method="POST" style="display:inline">
                                                @csrf
                                                @method('PUT')

                                                <button type="submit"
                                                    class="accion-boton"
                                                    title="Suspender/Habilitar">
                                                    <img
                                                        src="{{ $esSuspendido
                                                            ? asset('imagenes/IconoHabilitar.png')
                                                            : asset('imagenes/IconoSuspender.png') }}"
                                                        alt="Suspender/Habilitar"
                                                    >
                                                </button>
                                            </form>

                                            <form action="{{ route('grupos.destroy', $grupo->idGrupo) }}" method="POST" style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="button"
                                                    class="accion-boton"
                                                    title="Eliminar"
                                                    onclick="mostrarPopupConfirmacionGrupo('{{ $grupo->claveGrupo ?? 'grupo' }}', this)"
                                                >
                                                    <img
                                                        src="{{ $esSuspendido
                                                            ? asset('imagenes/IconoEliminarGris.png')
                                                            : asset('imagenes/IconoEliminar.png') }}"
                                                        alt="Eliminar"
                                                    />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </section>

        <div class="paginacion">
            {!! $grupos->links() !!}
        </div>
    </main>

    <script>
        function mostrarPopupConfirmacionGrupo(claveGrupo, boton) {
            const form = boton.closest('form');

            if (!form) {
                console.error('No se encontro el formulario de eliminacion.');
                return;
            }

            formularioAEliminar = form;

            const popup = document.getElementById('popupConfirmacion');
            const msg   = document.getElementById('mensajeConfirmacion');

            if (!popup || !msg) {
                console.error('No se encontro el popup global en barraNavegacion.');
                return;
            }

            msg.textContent = `Deseas eliminar el grupo "${claveGrupo}"?`;
            popup.style.display = 'flex';
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (typeof cerrarPopupConfirmacion === 'function') {
                    cerrarPopupConfirmacion();
                }
            }
        });
    </script>
</body>
</html>
