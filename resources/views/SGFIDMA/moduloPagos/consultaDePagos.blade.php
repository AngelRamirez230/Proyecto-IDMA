<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de pagos</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Lista de pagos</h1>

        <!-- =========================
            CONTROLES
        ========================== -->
        <section class="consulta-controles">

            <!-- BUSCADOR -->
            <form action="{{ route('consultaPagos') }}" method="GET">
                <div class="consulta-busqueda-group">
                    <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                    <input
                        type="text"
                        name="buscarPago"
                        placeholder="Ingresa número de referencia o estudiante"
                        value="{{ $buscar ?? '' }}"
                        onkeydown="if(event.key === 'Enter') this.form.submit();"
                    >
                </div>
            </form>

            <!-- FILTROS -->
            <div class="consulta-selects">
                <form action="{{ route('consultaPagos') }}" method="GET">

                    <input type="hidden" name="buscarPago" value="{{ $buscar ?? '' }}">

                    
                    <select name="filtro" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled {{ empty($filtro) ? 'selected' : '' }}>
                            Filtrar por
                        </option>
                        <option value="pendientes" {{ ($filtro ?? '') == 'pendientes' ? 'selected' : '' }}>
                            Pendientes
                        </option>
                        <option value="aprobados" {{ ($filtro ?? '') == 'aprobados' ? 'selected' : '' }}>
                            Aprobados
                        </option>
                        <option value="rechazados" {{ ($filtro ?? '') == 'rechazados' ? 'selected' : '' }}>
                            Rechazados
                        </option>
                    </select>
                    

                    <select name="orden" class="select select-boton" onchange="this.form.submit()">
                        <option value="" disabled {{ empty($orden) ? 'selected' : '' }}>
                            Ordenar por
                        </option>
                        <option value="alfabetico" {{ ($orden ?? '') == 'alfabetico' ? 'selected' : '' }}>
                            Alfabéticamente (A-Z)
                        </option>
                        <option value="porcentaje_mayor" {{ ($orden ?? '') == 'porcentaje_mayor' ? 'selected' : '' }}>
                            Más reciente
                        </option>
                        <option value="porcentaje_menor" {{ ($orden ?? '') == 'porcentaje_menor' ? 'selected' : '' }}>
                            Más antiguo
                        </option>
                    </select>

                </form>
            </div>

        </section>


        <div class="detalle-usuario__header">

            <!-- BOTÓN SUBIR TXT -->
            <form action="{{ route('pagos.validarArchivo') }}" method="POST" enctype="multipart/form-data" class="upload-form">
                @csrf
                <div class="upload-container">

                    <label for="archivoTxt" class="btn-upload">
                        Seleccionar archivo
                    </label>

                    <input 
                        type="file" 
                        name="archivoTxt" 
                        id="archivoTxt" 
                        accept=".txt,.xlsx,.xls" 
                        required 
                        class="upload-input-hidden"
                    >

                    <span id="archivoNombre" class="archivo-nombre">
                        Ningún documento seleccionado
                    </span>

                    <!-- Botón validar -->
                    <button type="submit" class="btn-boton-formulario2 btn-accion">
                        Validar pagos
                    </button>

                </div>
            </form>
        </div>

        <!-- =========================
            TABLA
        ========================== -->
        <section class="consulta-tabla-contenedor">
            <table class="tabla">

                <thead>
                    <tr class="tabla-encabezado">
                        <th>Nombre estudiante</th>
                        <th>Referencia de pago</th>
                        <th>Concepto de pago</th>
                        <th>Fecha límite de pago</th>
                        <th>Fecha de pago</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody class="tabla-cuerpo">

                    @if ($pagos->isEmpty())
                        <tr>
                            <td colspan="7" class="tablaVacia">
                                No existen pagos registrados.
                            </td>
                        </tr>
                    @else
                        @foreach ($pagos as $pago)
                            <tr class="{{ $pago->idEstatus == 2 ? 'fila-suspendida' : '' }}">

                                <td>
                                    {{ $pago->estudiante->usuario->primerNombre }}
                                    {{ $pago->estudiante->usuario->segundoNombre }}
                                    {{ $pago->estudiante->usuario->primerApellido }}
                                    {{ $pago->estudiante->usuario->segundoApellido }}
                                </td>

                                <td>{{ $pago->Referencia }}</td>

                                <td>{{ $pago->concepto->nombreConceptoDePago }}</td>

                                <td>
                                    {{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td>
                                    {{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td>{{ $pago->estatus->nombreTipoDeEstatus ?? 'Sin estatus' }}</td>

                                <td>
                                    <div class="tabla-acciones">
                                        <a href="{{ route('pagos.show', $pago->Referencia) }}"
                                            title="Ver detalles"
                                            class="btn-boton-formulario2 btn-accion">
                                                Ver detalles
                                        </a>

                                        @if($pago->idEstatus == 3)
                                            <a href="{{ route('pagos.recibo', $pago->Referencia) }}"
                                                class="btn-boton-formulario2 btn-accion"
                                                title="Descargar recibo">
                                                    Descargar recibo
                                            </a>
                                        @endif


                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    @endif

                </tbody>
            </table>

        </section>
        <!-- PAGINACIÓN -->
        <div class="paginacion">
            {{ $pagos->links() }}
        </div>

    </main>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('archivoTxt');
            const span  = document.getElementById('archivoNombre');

            if (input) {
                input.addEventListener('change', function () {
                    if (this.files && this.files.length > 0) {
                        span.textContent = this.files[0].name;
                    } else {
                        span.textContent = 'Ningún documento seleccionado';
                    }
                });
            }
        });
    </script>

</body>
</html>
