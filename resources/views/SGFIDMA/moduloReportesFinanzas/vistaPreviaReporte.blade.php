<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista previa del reporte</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @include('layouts.barraNavegacion')

    <main class="consulta">

        <!-- =========================
            BOTONES DE EXPORTACIÓN
        ========================== -->
        <div class="form-group2 acciones-reporte">

            <form action="{{ route('reportes.pdf') }}" method="POST">
                @csrf
                <input type="hidden" name="tipo" value="{{ $tipo }}">
                <input type="hidden" name="inicio" value="{{ $inicio }}">
                <input type="hidden" name="fin" value="{{ $fin }}">
                <button class="btn-boton-formulario2">Exportar PDF</button>
            </form>

            <form action="{{ route('reportes.excel') }}" method="POST">
                @csrf
                <input type="hidden" name="tipo" value="{{ $tipo }}">
                <input type="hidden" name="inicio" value="{{ $inicio }}">
                <input type="hidden" name="fin" value="{{ $fin }}">
                <button class="btn-boton-formulario2">Exportar Excel</button>
            </form>

            <a href="{{ url('/apartadoReporteFinanzas') }}"
               class="btn-boton-formulario2 btn-cancelar2">
                Cancelar
            </a>

        </div>

        <!-- =========================
            TABLA
        ========================== -->

        <h1 class="consulta-titulo" style="text-align:center;">
            Reporte de pagos {{ ($tipo) }}
        </h1>

        <p style="text-align:center; margin-bottom: 0px;">
            Del {{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }}
            al {{ \Carbon\Carbon::parse($fin)->format('d/m/Y') }}
        </p>

        <section class="consulta-tabla-contenedor">

            <table class="tabla">

                <thead>
                    <tr class="tabla-encabezado">
                        <th>Nombre estudiante</th>
                        <th>Referencia</th>
                        <th>Concepto</th>
                        <th>Fecha generación</th>
                        <th>Fecha límite</th>
                        <th>Fecha de pago</th>
                        <th>Estatus</th>
                    </tr>
                </thead>

                <tbody class="tabla-cuerpo">

                    @if ($pagos->isEmpty())
                        <tr>
                            <td colspan="7" class="tablaVacia">
                                No existen pagos en este rango de fechas.
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
                                    {{ $pago->fechaGeneracionDePago?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td>
                                    {{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td>
                                    {{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td>
                                    {{ $pago->estatus->nombreTipoDeEstatus ?? 'Sin estatus' }}
                                </td>

                            </tr>
                        @endforeach
                    @endif

                </tbody>
            </table>

        </section>

    </main>

</body>
</html>
