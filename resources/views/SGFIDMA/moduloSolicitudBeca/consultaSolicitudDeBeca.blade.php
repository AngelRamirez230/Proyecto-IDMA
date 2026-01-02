<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de solicitudes de beca</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Lista de solicitudes de beca</h1>

        <section class="consulta-controles">
            <form action="{{ route('consultaSolicitudBeca') }}" method="GET">
                <div class="consulta-busqueda-group">
                    <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                    <input type="text" id="buscarSolicitudDeBeca" name="buscarSolicitudDeBeca" placeholder="Ingresa nombre del estudiante o beca" value="{{ $buscar ?? '' }}" onkeydown="if(event.key === 'Enter') this.form.submit();"/>
                </div>
            </form>

            
            <div class="consulta-selects">
                <form action="{{ route('consultaSolicitudBeca') }}" method="GET" id="formFiltro">
                        <select name="filtro" class="select select-boton" onchange="this.form.submit()">
                            <option value="" disabled selected>Filtrar por</option>
                            <option value="todas" {{ ($filtro ?? '') == 'todas' ? 'selected' : '' }}>Ver todas</option>
                            <option value="pendientes" {{ ($filtro ?? '') == 'pendientes' ? 'selected' : '' }}>Pendientes de revisión</option>
                            <option value="aprobadas" {{ ($filtro ?? '') == 'aprobadas' ? 'selected' : '' }}>Aprobadas</option>
                            <option value="rechazadas" {{ ($filtro ?? '') == 'rechazadas' ? 'selected' : '' }}>Rechzadas</option>
                        </select>

                    <select name="orden" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled selected>Ordenar por</option>
                        <option value="mas_reciente" {{ ($orden ?? '') == 'mas_reciente' ? 'selected' : '' }}>
                            Mas reciente
                        </option>
                        <option value="menos_reciente" {{ ($orden ?? '') == 'menos_reciente' ? 'selected' : '' }}>
                            Menos reciente
                        </option>
                    </select>

                </form>
            </div>


        </section>

        <section class="consulta-tabla-contenedor">
            <table class="tabla" id="tablaSolicitudDeBeca">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Matricula alfanumérica</th>
                        <th>Nombre de estudiante</th>
                        <th>Beca solicitada</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="tabla-cuerpo">
                    @if ($solicitudes->isEmpty())
                        <tr>
                            <td colspan="5" class="tablaVacia"> No existen solicitudes de becas disponibles.</td>
                        </tr>
                    @else
                        @foreach ($solicitudes as $solicitud)

                            <td>{{ $solicitud->estudiante->matriculaAlfanumerica }}</td>
                            
                            <td>
                                {{ $solicitud->estudiante->usuario->primerNombre }}
                                {{ $solicitud->estudiante->usuario->segundoNombre }}
                                {{ $solicitud->estudiante->usuario->primerApellido }}
                                {{ $solicitud->estudiante->usuario->segundoApellido }}
                            </td>
                            <td>{{ $solicitud->beca->nombreDeBeca ?? 'Sin beca' }}</td>
                            <td>{{ $solicitud->estatus->nombreTipoDeEstatus ?? 'Sin estatus' }}</td>

                            <td>
                                <div class="tabla-acciones">
                            
                                    <!-- BOTÓN SOLICITAR BECA -->
                                    <a href="#" class="btn-boton-formulario2 btn-accion" title="Ver solicitud">
                                        Ver solicitud
                                    </a>


                                </div>
                            </td>
                        
                        @endforeach

                    @endif

                </tbody>
            </table>
        </section>
        <div class="paginacion">
            {!! $solicitudes->links() !!}
        </div>
    </main>
</body>
</html>