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
                    {{-- Aquí se iterarán las becas registradas --}}
                </tbody>
            </table>
        </section>
    </main>
    
</body>
</html>
