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

    @if (session('success'))
        <div class="popup-notificacion" id="popup">
            <div class="popup-contenido">
                <p>{{ session('success') }}</p>
                <button class="popup-boton" onclick="cerrarPopup()">Aceptar</button>
            </div>
        </div>
    @endif

    <main class="consulta">
        <h1 class="consulta-titulo">Lista de conceptos de pago</h1>

        <section class="consulta-controles">
            <form action="{{ route('consultaConcepto') }}">
                <div class="consulta-busqueda-group">
                    <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                    <input type="text" id="buscarConcepto" name="buscarConcepto" placeholder="Ingresa nombre del concepto de pago"value="{{ $buscar ?? '' }}" onkeydown="if(event.key === 'Enter') this.form.submit();"/>
                </div>
            </form>

            <div class="consulta-selects">
                <form action="{{ route('consultaConcepto') }}" method="GET" id="formFiltro">
                    <select name="filtro" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled selected>Filtrar por</option>
                        <option value="todas" {{ ($filtro ?? '') == 'todas' ? 'selected' : '' }}>Ver todas</option>
                        <option value="activas" {{ ($filtro ?? '') == 'activas' ? 'selected' : '' }}>Activo(a)</option>
                        <option value="suspendidas" {{ ($filtro ?? '') == 'suspendidas' ? 'selected' : '' }}>Suspendido(a)</option>
                    </select>

                    <select name="orden" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled selected>Ordenar por</option>
                        <option value="alfabetico" {{ ($orden ?? '') == 'alfabetico' ? 'selected' : '' }}>Alfabéticamente (A-Z)</option>
                        <option value="porcentaje_mayor" {{ ($orden ?? '') == 'porcentaje_mayor' ? 'selected' : '' }}>Mayor porcentaje</option>
                        <option value="porcentaje_menor" {{ ($orden ?? '') == 'porcentaje_menor' ? 'selected' : '' }}>Menor porcentaje</option>
                    </select>
                </form>
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

                    @forelse($conceptos as $concepto)
                        <tr class="tabla-fila {{ $concepto->idEstatus == 2 ? 'fila-suspendida' : '' }}">
                            <td>{{ $concepto->nombreConceptoDePago }}</td>
                            <td>${{ $concepto->costo }}</td>
                            <td>{{ $concepto->unidad->nombreUnidad ?? 'Sin unidad' }}</td>
                            <td>{{ $concepto->estatus->nombreTipoDeEstatus ?? 'Sin estatus' }}</td>
                            <td>
                                <div class="tabla-acciones">
                                    <!-- BOTÓN EDITAR -->
                                    <a href="{{route('concepto.edit', $concepto->idConceptoDePago)}}" class="accion-boton" title="Editar">
                                        <img 
                                            src="{{ $concepto->idEstatus == 2 
                                                ? asset('imagenes/IconoEditarGris.png') 
                                                : asset('imagenes/IconoEditar.png') }}" 
                                            alt="Editar">
                                    </a>

                                    <!-- BOTÓN SUSPENDER/HABILITAR -->
                                    <form action="{{ route('concepto.update', $concepto->idConceptoDePago) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" title="Suspender/Habilitar" class="accion-boton" name="accion" value="Suspender/Habilitar">

                                            <img 
                                                src="{{ $concepto->idEstatus == 2 
                                                    ? asset('imagenes/IconoHabilitar.png') 
                                                    : asset('imagenes/IconoSuspender.png') }}" 
                                                alt="Suspender/Habilitar"
                                            >
                                        </button>
                                    </form>

                                    <!-- BOTÓN ELIMINAR -->
                                    <form action="{{ route('concepto.destroy', $concepto->idConceptoDePago) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="accion-boton" title="Eliminar"
                                            onclick="mostrarPopupConfirmacion('{{ $concepto->nombreConceptoDePago }}', this)">
                                            <img 
                                                src="{{ $concepto->idEstatus == 2 
                                                    ? asset('imagenes/IconoEliminarGris.png') 
                                                    : asset('imagenes/IconoEliminar.png') }}" 
                                                alt="Eliminar"
                                            >
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No hay conceptos registrados.</td>
                        </tr>
                    @endforelse
                    
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
        function cerrarPopup() {
            document.getElementById('popup').style.display = 'none';
        }

        let formularioAEliminar = null;

        function mostrarPopupConfirmacion(nombreConceptoDePago, boton) {
            // Guardar el formulario del DELETE
            formularioAEliminar = boton.closest('form');

            // Cambiar texto del popup
            document.getElementById('mensajeConfirmacion').innerText =
                `¿Estás seguro de eliminar el concepto "${nombreConceptoDePago}"?`;

            // Mostrar popup
            document.getElementById('popupConfirmacion').style.display = 'flex';
        }

        function cerrarPopupConfirmacion() {
            document.getElementById('popupConfirmacion').style.display = 'none';
            formularioAEliminar = null;
        }

        // Enviar el formulario real DELETE
        function confirmarEliminacion() {
            if (formularioAEliminar) {
                formularioAEliminar.submit();
            }
        }
    </script>
    
</body>
</html>