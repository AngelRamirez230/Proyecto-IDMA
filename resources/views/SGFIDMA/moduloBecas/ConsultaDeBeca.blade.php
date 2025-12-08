<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Becas</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Lista de becas</h1>

        <section class="consulta-controles">
            <div class="consulta-busqueda-group">
                <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                <input
                    type="text"
                    id="buscarBeca"
                    name="buscarBeca"
                    placeholder="Ingresa nombre de beca o porcentaje de descuento"
                >
            </div>

            <div class="consulta-selects">
                <select type="button" class="select select-boton">
                    <option value="" disabled selected>Filtrar por</option>
                </select>

                <select type="button" class="select select-boton">
                    <option value="" disabled selected>Ordenar por</option>
                </select>
            </div>
        </section>

        <section class="consulta-tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Nombre de beca</th>
                        <th>Porcentaje de descuento</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="tabla-cuerpo">
                    @foreach ($becas as $beca)
                        <tr>
                            <td>{{ $beca->nombreDeBeca }}</td>
                            <td>{{ $beca->porcentajeDeDescuento }}%</td>
                            <td>{{ $beca->estatus->nombreTipoDeEstatus ?? 'Sin estatus' }}</td>
                            <td>
                                <div class="tabla-acciones">
                                    <a href="{{ route('becas.edit', $beca->idBeca) }}" class="accion-boton" title="Editar">
                                        <img src="{{ asset('imagenes/IconoEditar.png') }}" alt="Editar">
                                    </a>
                                    <a href="{{ route('becas.edit', $beca->idBeca) }}" class="accion-boton" title="Suspender">
                                        <img src="{{ asset('imagenes/IconoSuspender.png') }}" alt="Editar">
                                    </a>
                                    <a href="{{ route('becas.edit', $beca->idBeca) }}" class="accion-boton" title="Eliminar">
                                        <img src="{{ asset('imagenes/IconoEliminar.png') }}" alt="Eliminar">
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>

</body>
</html>
