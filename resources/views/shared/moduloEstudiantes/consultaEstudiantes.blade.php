<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Estudiantes</title>
    @vite(['resources/css/app.css'])
</head>
<body>

@include('layouts.barraNavegacion')

<main class="consulta">

    <h1 class="consulta-titulo">Lista de estudiantes</h1>

    
    <section class="consulta-controles">

        {{-- BUSCADOR --}}
        <form action="{{ route('estudiantes.index') }}" method="GET">
            <div class="consulta-busqueda-group">
                <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                <input
                    type="text"
                    name="buscarEstudiante"
                    placeholder="Ingresa nombre o matrícula del estudiante"
                    value="{{ request('buscarEstudiante') }}"
                    onkeydown="if(event.key === 'Enter') this.form.submit();"
                />
            </div>
        </form>

        
        <div class="consulta-selects">
            <form action="{{ route('estudiantes.index') }}" method="GET" id="formFiltro">

                <select name="filtro" class="select select-boton" onchange="this.form.submit()">
                    <option value="" disabled selected>Filtrar por</option>
                    <option value="todos" {{ request('filtro') == 'todos' ? 'selected' : '' }}>Ver todos</option>
                    <option value="activos" {{ request('filtro') == 'activos' ? 'selected' : '' }}>Activo(a)</option>
                    <option value="suspendidos" {{ request('filtro') == 'suspendidos' ? 'selected' : '' }}>Suspendido(a)</option>
                </select>

                <select name="orden" class="select select-boton" onchange="this.form.submit()">
                    <option value="" disabled selected>Ordenar por</option>
                    <option value="alfabetico" {{ request('orden') == 'alfabetico' ? 'selected' : '' }}>
                        Alfabéticamente (A-Z)
                    </option>
                </select>

                {{-- conservar búsqueda --}}
                <input type="hidden" name="buscar" value="{{ request('buscar') }}">

            </form>
        </div>

    </section>

    {{-- TABLA --}}
    <section class="consulta-tabla-contenedor">
        <table class="tabla" id="tablaEstudiantes">

            <thead>
                <tr class="tabla-encabezado">
                    <th>Matrícula</th>
                    <th>Nombre del estudiante</th>
                    <th>Generación</th>
                    <th>Semestre</th>
                    <th>Correo institucional</th>
                    <th>Estatus administrativo</th>
                    <th>Estatus académico</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody class="tabla-cuerpo">

                @forelse ($estudiantes as $estudiante)
                    <tr class="{{ $estudiante->usuario->idestatus == 2 ? 'fila-suspendida' : '' }}">

                        <td>{{ $estudiante->matriculaAlfanumerica }}</td>

                        <td>
                            {{ $estudiante->usuario->primerNombre }}
                            {{ $estudiante->usuario->segundoNombre }}
                            {{ $estudiante->usuario->primerApellido }}
                            {{ $estudiante->usuario->segundoApellido }}
                        </td>

                        <td>{{ $estudiante->generacion->nombreGeneracion }}</td>

                        <td>{{ $estudiante->grado }}</td>

                        <td>{{ $estudiante->usuario->correoInstitucional }}</td>

                        <td>
                            {{ $estudiante->usuario->estatus->nombreTipoDeEstatus}}
                        </td>

                        <td>
                            {{ $estudiante->estatus->nombreTipoDeEstatus}}
                        </td>

                        <td>
                            <div class="tabla-acciones">

                                {{-- EDITAR --}}
                                <a href="{{ route('estudiantes.edit', $estudiante->idEstudiante) }}"
                                   class="accion-boton"
                                   title="Editar">

                                    <img
                                        src="{{ $estudiante->usuario->idestatus == 2
                                            ? asset('imagenes/IconoEditarGris.png')
                                            : asset('imagenes/IconoEditar.png') }}"
                                        alt="Editar">
                                </a>

                                {{-- SUSPENDER / HABILITAR --}}
                                <form action="{{ route('estudiantes.update', $estudiante->idEstudiante) }}"
                                      method="POST"
                                      style="display:inline"> 

                                    @csrf
                                    @method('PUT')

                                    <button type="submit" class="accion-boton" name="accion" title="Suspender/Habilitar" value="Suspender/Habilitar" >
                                        <img
                                            src="{{ $estudiante->usuario->idestatus == 2
                                                ? asset('imagenes/IconoHabilitar.png')
                                                : asset('imagenes/IconoSuspender.png') }}"
                                            alt="Suspender / Habilitar">
                                    </button>
                                </form>

                                {{-- ELIMINAR --}}
                                <form action="#"
                                      method="POST"
                                      style="display:inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                            class="accion-boton"
                                            title="Eliminar"
                                            onclick="mostrarPopupConfirmacion('{{ $estudiante->matriculaAlfanumerica }}', this)">
                                        <img
                                            src="{{ $estudiante->usuario->idestatus == 2
                                                ? asset('imagenes/IconoEliminarGris.png')
                                                : asset('imagenes/IconoEliminar.png') }}"
                                            alt="Eliminar">
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="tablaVacia">
                            No existen estudiantes registrados.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </section>

    {{-- PAGINACIÓN --}}
    <div class="paginacion">
        {{ $estudiantes->links() }}
    </div>

</main>

{{-- POPUP --}}
<script>
    function mostrarPopupConfirmacion(matricula, boton) {
        formularioAEliminar = boton.closest('form');
        document.getElementById('mensajeConfirmacion').innerText =
            `¿Estás seguro de eliminar al estudiante con matrícula "${matricula}"?`;
        document.getElementById('popupConfirmacion').style.display = 'flex';
    }
</script>

</body>
</html>
