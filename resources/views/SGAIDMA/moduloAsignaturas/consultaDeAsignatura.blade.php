<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Consulta de asignaturas</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Lista de asignaturas</h1>

        <section class="consulta-controles">
            <form action="{{ route('consultaAsignatura') }}" method="GET">
                <div class="consulta-busqueda-group">
                    <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                    <input
                        type="text"
                        id="buscarAsignatura"
                        name="buscarAsignatura"
                        placeholder="Ingresa nombre, clave o plan de estudios"
                        value="{{ $buscar ?? '' }}"
                        onkeydown="if(event.key === 'Enter') this.form.submit();"
                    >
                </div>

                <input type="hidden" name="orden" value="{{ $orden ?? '' }}">
            </form>

            <div class="consulta-selects">
                <form action="{{ route('consultaAsignatura') }}" method="GET" id="formOrdenAsignaturas">
                    <input type="hidden" name="buscarAsignatura" value="{{ $buscar ?? '' }}">

                    <select name="orden" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled {{ empty($orden) ? 'selected' : '' }}>Ordenar por</option>
                        <option value="nombre" {{ ($orden ?? '') === 'nombre' ? 'selected' : '' }}>Nombre</option>
                        <option value="semestre" {{ ($orden ?? '') === 'semestre' ? 'selected' : '' }}>Semestre</option>
                        <option value="creditos" {{ ($orden ?? '') === 'creditos' ? 'selected' : '' }}>Creditos</option>
                        <option value="plan" {{ ($orden ?? '') === 'plan' ? 'selected' : '' }}>Plan de estudios</option>
                    </select>
                </form>
            </div>
        </section>

        <section class="consulta-tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Nombre</th>
                        <th>Clave</th>
                        <th>Créditos</th>
                        <th>Semestre</th>
                        <th>Plan de estudios</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="tabla-cuerpo">
                    @if ($asignaturas->isEmpty())
                        <tr>
                            <td colspan="6" class="tablaVacia">
                                @if(!empty($buscar))
                                    No se encontraron coincidencias con la búsqueda realizada.
                                @else
                                    No existen asignaturas registradas.
                                @endif
                            </td>
                        </tr>
                    @else
                        @foreach ($asignaturas as $asignatura)
                            <tr class="tabla-fila">
                                <td>{{ $asignatura->nombre ?? 'Sin nombre' }}</td>
                                <td>{{ $asignatura->claveAsignatura ?? 'Sin clave' }}</td>
                                <td>{{ $asignatura->creditos ?? '-' }}</td>
                                <td>{{ $asignatura->semestre ?? '-' }}</td>
                                <td>{{ $asignatura->nombrePlanDeEstudios ?? 'Sin plan' }}</td>
                                <td>
                                    <div class="tabla-acciones">
                                        <a href="{{ route('asignaturas.show', $asignatura->idAsignatura) }}"
                                            class="accion-boton"
                                            title="Ver detalles">
                                            <img src="{{ asset('imagenes/IconoDetallesAsignatura.png') }}" alt="Ver">
                                        </a>

                                        @if(!empty($puedeEditar))
                                            <a href="{{ route('asignaturas.edit', $asignatura->idAsignatura) }}"
                                                class="accion-boton"
                                                title="Editar">
                                                <img src="{{ asset('imagenes/IconoEditar.png') }}" alt="Editar">
                                            </a>
                                        @endif

                                        @if(auth()->check() && (int)auth()->user()->idtipoDeUsuario === 1)
                                            <form action="{{ route('asignaturas.destroy', $asignatura->idAsignatura) }}" method="POST" style="display:inline">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="button"
                                                    class="accion-boton"
                                                    title="Eliminar"
                                                    onclick="mostrarPopupConfirmacionAsignatura('{{ $asignatura->nombre ?? 'la asignatura' }}', this)"
                                                >
                                                    <img src="{{ asset('imagenes/IconoEliminar.png') }}" alt="Eliminar" />
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
            {!! $asignaturas->links() !!}
        </div>
    </main>

    <script>
        function mostrarPopupConfirmacionAsignatura(nombreAsignatura, boton) {
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

            msg.textContent = `Deseas eliminar "${nombreAsignatura}"?`;
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
