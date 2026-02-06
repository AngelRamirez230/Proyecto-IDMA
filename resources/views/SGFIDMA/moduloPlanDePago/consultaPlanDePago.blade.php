<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de plan de pago</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')
    
    <main class="consulta">
        <h1 class="consulta-titulo">Lista de planes de pago</h1>

        <section class="consulta-controles">
            <form action="{{ route('consultaPlan') }}" method="GET">
                <div class="consulta-busqueda-group">
                    <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                    <input type="text" id="buscarPlan" name="buscarPlan" placeholder="Ingresa nombre del plan de pago" value="{{ $buscar ?? '' }}" onkeydown="if(event.key === 'Enter') this.form.submit();"/>
                </div>
            </form>

            <div class="consulta-selects">
                <form action="{{ route('consultaPlan') }}" method="GET" id="formFiltro">
                    <input type="hidden" name="buscarPlan" value="{{ $buscar ?? '' }}">

                    <select name="filtro" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled selected>Filtrar por</option>
                        <option value="todas" {{ ($filtro ?? '') == 'todas' ? 'selected' : '' }}>Ver todas</option>
                        <option value="activas" {{ ($filtro ?? '') == 'activas' ? 'selected' : '' }}>Activo(a)</option>
                        <option value="suspendidas" {{ ($filtro ?? '') == 'suspendidas' ? 'selected' : '' }}>Suspendido(a)</option>
                    </select>

                    <select name="orden" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled selected>Ordenar por</option>
                        <option value="alfabetico" {{ ($orden ?? '') == 'alfabetico' ? 'selected' : '' }}>Alfabéticamente (A-Z)</option>
                    </select>
                </form>
            </div>

        </section>

        <section class="consulta-tabla-contenedor">
            <table class="tabla" id="tablaBecas">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Plan de pago</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="tabla-cuerpo">
                    @if ($planes->isEmpty())
                        <tr>
                            <td colspan="3" class="tablaVacia"> No existen planes de pago disponibles.</td>
                        </tr>
                    @else
                        @foreach ($planes as $plan)
                            <tr class="{{ $plan->idEstatus == 2 ? 'fila-suspendida' : '' }}">
                                <td>{{ $plan->nombrePlanDePago }}</td>
                                <td>{{ $plan->estatus->nombreTipoDeEstatus ?? 'Sin estatus' }}</td>

                                <td>
                                    <div class="tabla-acciones">

                                        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11))
                                            <!-- BOTÓN EDITAR -->
                                            <a href="{{ route('planes.edit', $plan->idPlanDePago) }}" class="accion-boton" title="Editar">
                                                <img 
                                                    src="{{ $plan->idEstatus == 2 
                                                        ? asset('imagenes/IconoEditarGris.png') 
                                                        : asset('imagenes/IconoEditar.png') }}" 
                                                    alt="Editar">
                                            </a>

                                            <!-- BOTÓN SUSPENDER/HABILITAR -->
                                            <form action="{{ route('planes.update', $plan->idPlanDePago) }}" method="POST" style="display:inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" title="Suspender/Habilitar" class="accion-boton" name="accion" value="Suspender/Habilitar">

                                                    <img 
                                                        src="{{ $plan->idEstatus == 2 
                                                            ? asset('imagenes/IconoHabilitar.png') 
                                                            : asset('imagenes/IconoSuspender.png') }}" 
                                                        alt="Suspender/Habilitar"
                                                    >

                                                </button>
                                            </form>

                                            @admin
                                                <!-- BOTÓN ELIMINAR -->
                                                <form action="{{ route('planes.destroy', $plan->idPlanDePago) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="accion-boton" title="Eliminar"
                                                        onclick="mostrarPopupConfirmacion('{{ $plan->nombrePlanDePago }}', this)">
                                                        <img 
                                                            src="{{ $plan->idEstatus == 2 
                                                                ? asset('imagenes/IconoEliminarGris.png') 
                                                                : asset('imagenes/IconoEliminar.png') }}" 
                                                            alt="Eliminar"
                                                        >
                                                    </button>
                                                </form>
                                            @endadmin
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    @endif

                </tbody>
            </table>
        </section>
        <div class="paginacion">
            {!! $planes->links() !!}
        </div>
    </main>


    <script>

        function mostrarPopupConfirmacion(nombreBeca, boton) {
            // Guardamos el formulario DELETE
            formularioAEliminar = boton.closest('form');

            // Cambiar texto del popup
            document.getElementById('mensajeConfirmacion').innerText =
                `¿Estás seguro de eliminar el plan de pago "${nombreBeca}"?`;

            // Mostrar popup
            document.getElementById('popupConfirmacion').style.display = 'flex';
        }


    </script>


</body>
</html>
