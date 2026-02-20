<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar pagos</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Eliminar pagos</h1>

        <!-- =========================
            CONTROLES
        ========================== -->
        <section class="consulta-controles">

            <!-- BUSCADOR -->
            <form action="{{ route('pagos.eliminar.vista') }}" method="GET">
                <div class="consulta-busqueda-group">
                    <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                    <input
                        type="text"
                        name="buscarPago"
                        placeholder="{{ 
                            (Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11))
                            ? 'Ingresa número de referencia o estudiante'
                            : 'Ingresa número de referencia'
                        }}"
                        value="{{ $buscar ?? '' }}"
                        onkeydown="if(event.key === 'Enter') this.form.submit();"
                    >
                </div>
            </form>

        </section>

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
                        <th>Monto</th>
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
                                @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11))
                                    <td>
                                        {{ $pago->estudiante->usuario->primerNombre }}
                                        {{ $pago->estudiante->usuario->segundoNombre }}
                                        {{ $pago->estudiante->usuario->primerApellido }}
                                        {{ $pago->estudiante->usuario->segundoApellido }}
                                    </td>
                                @endif

                                <td>{{ $pago->Referencia }}</td>

                                <td>{{ $pago->concepto->nombreConceptoDePago }}</td>

                                <td>${{ $pago->montoAPagar }}</td>

                                <td>
                                    {{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td>
                                    {{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td>{{ $pago->estatus->nombreTipoDeEstatus ?? 'Sin estatus' }}</td>

                                <td>
                                    <form action="{{ route('pagos.destroy', $pago->Referencia) }}" 
                                        method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="accion-boton"
                                                onclick="return confirm('¿Eliminar este pago?')"
                                                title="Eliminar">

                                            <img src="{{ asset('imagenes/IconoEliminar.png') }}" 
                                                alt="Eliminar">
                                        </button>
                                    </form>
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
    
</body>
</html>