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
            <form action="{{ route('consultaBeca') }}" method="GET">
                <div class="consulta-busqueda-group">
                    <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                    <input type="text" id="buscarBeca" name="buscarBeca" placeholder="Ingresa nombre de la beca" value="{{ $buscar ?? '' }}" onkeydown="if(event.key === 'Enter') this.form.submit();"/>
                </div>
            </form>

            <div class="consulta-selects">
                <form action="{{ route('consultaBeca') }}" method="GET" id="formFiltro">
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

        <section class="consulta-tabla-contenedor">
            <table class="tabla" id="tablaBecas">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Nombre de beca</th>
                        <th>Porcentaje de descuento</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="tabla-cuerpo">
                    @if ($becas->isEmpty())
                        <tr>
                            <td colspan="4" class="tablaVacia"> No existen becas disponibles.</td>
                        </tr>
                    @else
                        @foreach ($becas as $beca)
                            <tr class="{{ $beca->idEstatus == 2 ? 'fila-suspendida' : '' }}">
                                <td>{{ $beca->nombreDeBeca }}</td>
                                <td>{{ $beca->porcentajeDeDescuento }}%</td>
                                <td>{{ $beca->estatus->nombreTipoDeEstatus ?? 'Sin estatus' }}</td>

                                <td>
                                    <div class="tabla-acciones">

                                        <!-- BOTÓN EDITAR -->
                                        <a href="{{ route('becas.edit', $beca->idBeca) }}" class="accion-boton" title="Editar">
                                            <img 
                                                src="{{ $beca->idEstatus == 2 
                                                    ? asset('imagenes/IconoEditarGris.png') 
                                                    : asset('imagenes/IconoEditar.png') }}" 
                                                alt="Editar">
                                        </a>

                                        <!-- BOTÓN SUSPENDER/HABILITAR -->
                                        <form action="{{ route('becas.update', $beca->idBeca) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" title="Suspender/Habilitar" class="accion-boton" name="accion" value="Suspender/Habilitar">

                                                <img 
                                                    src="{{ $beca->idEstatus == 2 
                                                        ? asset('imagenes/IconoHabilitar.png') 
                                                        : asset('imagenes/IconoSuspender.png') }}" 
                                                    alt="Suspender/Habilitar"
                                                >

                                            </button>
                                        </form>

                                        <!-- BOTÓN ELIMINAR -->
                                        <form action="{{ route('becas.destroy', $beca->idBeca) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="accion-boton" title="Eliminar"
                                                onclick="mostrarPopupConfirmacion('{{ $beca->nombreDeBeca }}', this)">
                                                <img 
                                                    src="{{ $beca->idEstatus == 2 
                                                        ? asset('imagenes/IconoEliminarGris.png') 
                                                        : asset('imagenes/IconoEliminar.png') }}" 
                                                    alt="Eliminar"
                                                >
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    @endif

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
        <div class="paginacion">
            {!! $becas->links() !!}
        </div>
    </main>

    <script>

        function mostrarPopupConfirmacion(nombreBeca, boton) {
            // Guardamos el formulario DELETE
            formularioAEliminar = boton.closest('form');

            // Cambiar texto del popup
            document.getElementById('mensajeConfirmacion').innerText =
                `¿Estás seguro de eliminar la beca "${nombreBeca}"?`;

            // Mostrar popup
            document.getElementById('popupConfirmacion').style.display = 'flex';
        }


    </script>

</body>
</html>
