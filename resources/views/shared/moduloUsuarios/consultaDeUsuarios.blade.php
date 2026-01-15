<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Consulta de Usuarios</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Lista de usuarios</h1>

        <section class="consulta-controles">

            {{-- BÚSQUEDA --}}
            <form action="{{ route('consultaUsuarios') }}" method="GET">
                <div class="consulta-busqueda-group">
                    <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                    <input
                        type="text"
                        id="buscarUsuario"
                        name="buscarUsuario"
                        placeholder="Ingresa nombre, correo o rol"
                        value="{{ $buscar ?? '' }}"
                        onkeydown="if(event.key === 'Enter') this.form.submit();"
                    >
                </div>

                {{-- Mantener filtro/orden cuando se busca --}}
                <input type="hidden" name="filtro" value="{{ $filtro ?? '' }}">
                <input type="hidden" name="orden" value="{{ $orden ?? '' }}">
            </form>

            {{-- FILTRO + ORDEN --}}
            <div class="consulta-selects">
                <form action="{{ route('consultaUsuarios') }}" method="GET" id="formFiltroUsuarios">

                    {{-- Mantener búsqueda al filtrar/ordenar --}}
                    <input type="hidden" name="buscarUsuario" value="{{ $buscar ?? '' }}">

                    <select name="filtro" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled {{ empty($filtro) ? 'selected' : '' }}>Filtrar por</option>
                        <option value="todos" {{ ($filtro ?? '') === 'todos' ? 'selected' : '' }}>Ver todos</option>
                        <option value="activos" {{ ($filtro ?? '') === 'activos' ? 'selected' : '' }}>Activos</option>
                        <option value="suspendidos" {{ ($filtro ?? '') === 'suspendidos' ? 'selected' : '' }}>Suspendidos</option>
                        <option value="eliminados" {{ ($filtro ?? '') === 'eliminados' ? 'selected' : '' }}>Eliminados</option>
                    </select>

                    <select name="orden" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled {{ empty($orden) ? 'selected' : '' }}>Ordenar por</option>
                        <option value="alfabetico" {{ ($orden ?? '') === 'alfabetico' ? 'selected' : '' }}>Alfabéticamente</option>
                        <option value="recientes" {{ ($orden ?? '') === 'recientes' ? 'selected' : '' }}>Más recientes</option>
                    </select>

                </form>
            </div>
        </section>

        {{-- TABLA --}}
        <section class="consulta-tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Nombre completo</th>
                        <th>Rol asignado</th>
                        <th>Correo institucional</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody class="tabla-cuerpo">
                    @if ($usuarios->isEmpty())
                        <tr>
                            <td colspan="5" class="tablaVacia">
                                No existen usuarios registrados.
                            </td>
                        </tr>
                    @else
                        @foreach ($usuarios as $usuario)
                            @php
                                $nombreCompleto = trim(
                                    ($usuario->primerNombre ?? '') . ' ' .
                                    ($usuario->segundoNombre ?? '') . ' ' .
                                    ($usuario->primerApellido ?? '') . ' ' .
                                    ($usuario->segundoApellido ?? '')
                                );
                                $rol = $usuario->tipoDeUsuario->nombreTipoDeUsuario ?? 'Sin rol';
                                $estatus = $usuario->estatus->nombreTipoDeEstatus ?? 'Sin estatus';

                                $filaSuspendida = ((int)($usuario->idestatus ?? 0) === 2) ? 'fila-suspendida' : '';
                            @endphp

                            @php
                                $esEliminado = ((int)($usuario->idestatus ?? 0) === 8);
                            @endphp

                            @if($esEliminado)
                                <span class="accion-boton" style="opacity:.4; pointer-events:none;">
                                    <img src="{{ asset('imagenes/IconoEditarGris.png') }}" alt="No disponible">
                                </span>
                            @else
                                {{-- botones normales --}}
                            @endif

                            <tr class="tabla-fila {{ $filaSuspendida }}">
                                <td>{{ $nombreCompleto ?: 'Sin nombre' }}</td>
                                <td>{{ $rol }}</td>
                                <td>{{ $usuario->correoInstitucional ?? 'Sin correo' }}</td>
                                <td>{{ $estatus }}</td>
                                <td>
                                    <div class="tabla-acciones">

                                        {{-- VER (placeholder) --}}
                                        @if(((int)($usuario->idestatus ?? 0) === 2))
                                            <span class="accion-boton" title="No disponible (suspendido)" style="pointer-events:none; opacity:.6;">
                                                <img src="{{ asset('imagenes/IconoVerUsuarioGris.png') }}" alt="Ver">
                                            </span>
                                        @else
                                        <a href="{{ route('usuarios.show', $usuario->idUsuario) }}"
                                            class="accion-boton"
                                            title="Ver detalles">
                                            <img src="{{ asset('imagenes/IconoVerUsuario.png') }}" alt="Ver">
                                        </a>
                                        @endif

                                        {{-- EDITAR --}}
                                        @if(((int)($usuario->idestatus ?? 0) === 2))
                                            <span class="accion-boton" title="No disponible (suspendido)" style="pointer-events:none; opacity:.6;">
                                                <img src="{{ asset('imagenes/IconoEditarGris.png') }}" alt="Editar">
                                            </span>
                                        @else
                                            <a href="{{ route('usuarios.edit', $usuario->idUsuario) }}"
                                            class="accion-boton"
                                            title="Editar">
                                                <img src="{{ asset('imagenes/IconoEditar.png') }}" alt="Editar">
                                            </a>
                                        @endif

                                        {{-- SUSPENDER/HABILITAR --}}
                                        <form action="{{ route('usuarios.toggleEstatus', $usuario->idUsuario) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('PUT')

                                            <button type="submit" class="accion-boton" title="Suspender/Habilitar">
                                                <img
                                                    src="{{ ((int)($usuario->idestatus ?? 0) === 2)
                                                        ? asset('imagenes/IconoHabilitar.png')
                                                        : asset('imagenes/IconoSuspender.png') }}"
                                                    alt="Suspender/Habilitar"
                                                >
                                            </button>
                                        </form>

                                        {{-- ELIMINAR --}}
                                        <form action="{{ route('usuarios.destroy', $usuario->idUsuario) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="button"
                                                class="accion-boton"
                                                title="Eliminar"
                                                {{ ((int)($usuario->idestatus ?? 0) === 8) ? 'disabled' : '' }}
                                                onclick="mostrarPopupConfirmacionUsuario(
                                                    '{{ trim(($usuario->primerNombre ?? '').' '.($usuario->primerApellido ?? '')) }}',
                                                    this
                                                )"
                                            >
                                                <img
                                                    src="{{ ((int)($usuario->idestatus ?? 0) === 2 || (int)($usuario->idestatus ?? 0) === 8)
                                                        ? asset('imagenes/IconoEliminarGris.png')
                                                        : asset('imagenes/IconoEliminar.png') }}"
                                                    alt="Eliminar"
                                                />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </section>

        {{-- PAGINACIÓN --}}
        <div class="paginacion">
            {!! $usuarios->links() !!}
        </div>
    </main>

    <script>
        /**
         * Este script usa el popup global que viene en barraNavegacion.blade.php:
         *  - #popupConfirmacion
         *  - #mensajeConfirmacion
         *  - variable global: formularioAEliminar
         *  - función global: confirmarEliminacion()
         *  - función global: cerrarPopupConfirmacion()
         */

        function mostrarPopupConfirmacionUsuario(nombreUsuario, boton) {
            // 1) Encontrar el <form> que envía DELETE
            const form = boton.closest('form');

            if (!form) {
                console.error('No se encontró el <form> del botón eliminar. Asegúrate de que el botón esté dentro de un <form method="POST">.');
                return;
            }

            // 2) Guardar en la variable global del layout (barraNavegacion)
            // (NO redeclarar formularioAEliminar aquí)
            formularioAEliminar = form;

            // 3) Referencias al popup global
            const popup = document.getElementById('popupConfirmacion');
            const msg   = document.getElementById('mensajeConfirmacion');

            if (!popup || !msg) {
                console.error('No se encontró el popup global en barraNavegacion (#popupConfirmacion / #mensajeConfirmacion).');
                return;
            }

            // 4) Mensaje + mostrar
            msg.textContent = `¿Estás seguro de eliminar al usuario "${nombreUsuario}"?`;
            popup.style.display = 'flex';
        }

        // Opcional: cerrar con ESC también desde esta vista (sin romper otras)
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                // Esta función existe en barraNavegacion
                if (typeof cerrarPopupConfirmacion === 'function') {
                    cerrarPopupConfirmacion();
                }
            }
        });
    </script>
</body>
</html>