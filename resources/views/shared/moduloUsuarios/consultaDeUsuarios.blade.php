<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

                                // si manejas idestatus 2 como suspendido, puedes añadir clase
                                $filaSuspendida = ((int)($usuario->idestatus ?? 0) === 2) ? 'fila-suspendida' : '';
                            @endphp

                            <tr class="tabla-fila {{ $filaSuspendida }}">
                                <td>{{ $nombreCompleto ?: 'Sin nombre' }}</td>
                                <td>{{ $rol }}</td>
                                <td>{{ $usuario->correoInstitucional ?? 'Sin correo' }}</td>
                                <td>{{ $estatus }}</td>
                                <td>
                                    <div class="tabla-acciones">

                                        {{-- VER (placeholder) --}}
                                        <button type="button" class="accion-boton" title="Ver detalles">
                                            <img src="{{ asset('imagenes/IconoInicioUsuarios.png') }}" alt="Ver">
                                        </button>

                                        {{-- EDITAR (placeholder) --}}
                                        <button type="button" class="accion-boton" title="Editar">
                                            <img
                                                src="{{ ((int)($usuario->idestatus ?? 0) === 2)
                                                    ? asset('imagenes/IconoEditarGris.png')
                                                    : asset('imagenes/IconoEditar.png') }}"
                                                alt="Editar"
                                            >
                                        </button>

                                        {{-- SUSPENDER/HABILITAR (placeholder) --}}
                                        <button type="button" class="accion-boton" title="Suspender/Habilitar">
                                            <img
                                                src="{{ ((int)($usuario->idestatus ?? 0) === 2)
                                                    ? asset('imagenes/IconoHabilitar.png')
                                                    : asset('imagenes/IconoSuspender.png') }}"
                                                alt="Suspender/Habilitar"
                                            >
                                        </button>

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
</body>
</html>