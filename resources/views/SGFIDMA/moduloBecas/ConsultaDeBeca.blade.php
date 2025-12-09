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

    @if (session('success'))
        <div class="popup-notificacion" id="popup">
            <div class="popup-contenido">
                <p>{{ session('success') }}</p>
                <button class="popup-boton" onclick="cerrarPopup()">Aceptar</button>
            </div>
        </div>
    @endif

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
                                    <form action="{{ route('becas.update', $beca->idBeca) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" title="Suspender/Habilitar" class="accion-boton" name="accion" value="Suspender/Habilitar">
                                            <img src="{{ asset('imagenes/IconoSuspender.png') }}" alt="Suspender">
                                        </button>
                                    </form>
                                    <form action="{{ route('becas.destroy', $beca->idBeca) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="accion-boton" title="Eliminar"
                                            onclick="mostrarPopupConfirmacion('{{ $beca->nombreDeBeca }}', this)">
                                            <img src="{{ asset('imagenes/IconoEliminar.png') }}" alt="Eliminar">
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="popup-confirmacion" id="popupConfirmacion">
                <div class="popup-contenido">
                    <p id="mensajeConfirmacion">¿Seguro?</p>
                    <div class="popup-botones">
                        <button class="btn-confirmar" onclick="confirmarEliminacion()">Eliminar</button>
                        <button class="btn-cancelar-confirmacion" onclick="cerrarPopupConfirmacion()">Cancelar</button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Cerrar popup de notificación
        function cerrarPopup() {
            document.getElementById('popup').style.display = 'none';
        }

        let formularioAEliminar = null;

        function mostrarPopupConfirmacion(nombreBeca, boton) {
            // Guardamos el formulario DELETE
            formularioAEliminar = boton.closest('form');

            // Cambiar texto del popup
            document.getElementById('mensajeConfirmacion').innerText =
                `¿Estás seguro de eliminar la beca "${nombreBeca}"?`;

            // Mostrar popup
            document.getElementById('popupConfirmacion').style.display = 'flex';
        }

        function cerrarPopupConfirmacion() {
            document.getElementById('popupConfirmacion').style.display = 'none';
            formularioAEliminar = null;
        }

        // Enviar el formulario real
        function confirmarEliminacion() {
            if (formularioAEliminar) {
                formularioAEliminar.submit(); // ← AQUÍ SI SE ENVÍA DELETE
            }
        }
    </script>


    

</body>
</html>
