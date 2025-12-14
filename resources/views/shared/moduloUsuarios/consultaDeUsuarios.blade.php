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
            <!-- Barra de búsqueda -->
            <div class="consulta-busqueda-group">
                <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                <input
                    type="text"
                    id="buscarUsuario"
                    name="buscarUsuario"
                    placeholder="Ingresa nombre, correo o rol"
                >
            </div>

            <!-- Filtros del lado derecho -->
            <div class="consulta-selects">
                <select type="button" class="select select-boton"> 
                    <option value="" disabled selected>Filtrar por</option>
                </select>

                <select type="button" class="select select-boton">
                    <option value="" disabled selected>Ordenar por</option>
                </select>
            </div>
        </section>

        <!-- Tabla de resultados -->
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
                    {{-- Aquí se iterarán los usuarios registrados --}}
                    {{--
                    @foreach($usuarios as $usuario)
                        <tr class="tabla-fila">
                            <td>{{ $usuario->nombre_completo }}</td>
                            <td>{{ $usuario->rol_asignado }}</td>
                            <td>{{ $usuario->correo_institucional }}</td>
                            <td>{{ $usuario->estatus }}</td>
                            <td>
                                <div class="tabla-acciones">
                                    <button type="button" class="accion-boton" title="Ver detalles">
                                        <img src="{{ asset('imagenes/IconoInicioUsuarios.png') }}" alt="Ver">
                                    </button>
                                    <button type="button" class="accion-boton" title="Editar">
                                        <img src="{{ asset('imagenes/IconoInicioUsuarios.png') }}" alt="Editar">
                                    </button>
                                    <button type="button" class="accion-boton" title="Suspender">
                                        <img src="{{ asset('imagenes/IconoInicioUsuarios.png') }}" alt="Desactivar">
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    --}}
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
