<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de conceptos de pago</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Lista de conceptos de pago</h1>

        <section class="consulta-controles">
            <!-- Barra de búsqueda -->
            <div class="consulta-busqueda-group">
                <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                <input
                    type="text"
                    id="buscarConcepto"
                    name="buscarConcepto"
                    placeholder="Ingresa nombre del concepto de pago"
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
                        <th>Concepto de pago</th>
                        <th>Costo</th>
                        <th>Unidad</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="tabla-cuerpo">
                    {{-- Aquí se iterarán las becas registradas --}}
                    {{--
                    @foreach($concepto_de_pago as $concepto)
                        <tr class="tabla-fila">
                            <td>{{ $concepto->nombreDeBeca }}</td>
                            <td>{{ $concepto->porcentajeDeDescuento }}</td>
                            <td>{{ $concepto->unidad }}</td>
                            <td>{{ $concepto->estatus }}</td>
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