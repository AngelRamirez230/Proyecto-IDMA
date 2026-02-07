<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista previa del reporte</title>
    @php use Illuminate\Support\Str; @endphp
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

        
        @if ($tipo === 'kardex')

            <section class="consulta-tabla-contenedor">

                <table class="tabla">
                    <tbody class="tabla-cuerpo">

                        {{-- =========================
                            ENCABEZADO DEL ESTUDIANTE
                        ========================== --}}
                        <tr>
                            <td colspan="9">KARDEX DE PAGOS</td>
                        </tr>

                        <tr>
                            <td colspan="1">Nombre:</td>
                            <td colspan="8">
                                {{ Str::upper(
                                    $estudiante->usuario->primerNombre . ' ' .
                                    $estudiante->usuario->segundoNombre . ' ' .
                                    $estudiante->usuario->primerApellido . ' ' .
                                    $estudiante->usuario->segundoApellido
                                ) }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="1">Carrera:</td>
                            <td colspan="8">
                                {{ mb_strtoupper($estudiante->planDeEstudios->licenciatura->nombreLicenciatura ?? '-', 'UTF-8') }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="1"><strong>Matrícula</strong></td>
                            <td colspan="1"><strong>{{ $estudiante->matriculaAlfanumerica ?? '-' }}</strong></td>

                            <td colspan="1">Generación:</td>
                            <td colspan="6">{{ $estudiante->generacion->nombreGeneracion ?? '-' }}</td>
                        </tr>

                        {{-- =========================
                            ENCABEZADO DE MOVIMIENTOS
                        ========================== --}}
                        <tr class="tabla-encabezado">
                            <th>Concepto</th>
                            <th>Semestre o mes</th>
                            <th>Cantidad</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th colspan="3">Forma de pago</th>
                            <th>Saldo</th>
                        </tr>

                        {{-- =========================
                            MOVIMIENTOS DEL KÁRDEX
                        ========================== --}}

                        @php
                            $saldo = 0;
                        @endphp

                        @forelse ($kardex as $fila)
                            
                        
                            @php
                                if (($fila['estado'] ?? null) === 11 && !empty($fila['monto'])) {
                                    $saldo += $fila['monto'];
                                }
                            @endphp





                            <tr>
                                {{-- CONCEPTO --}}
                                <td>
                                    @if ($fila['tipo'] === 'semestre')
                                        <strong>{{ $fila['concepto'] }}</strong>
                                    @else
                                        {{ $fila['concepto'] }}
                                    @endif
                                </td>

                                {{-- PERIODO --}}
                                <td>
                                    @if ($fila['tipo'] === 'semestre')
                                        <strong>{{ $fila['periodo'] }}</strong>
                                    @else
                                        {{ $fila['periodo'] }}
                                    @endif
                                </td>

                                {{-- CANTIDAD --}}
                                <td>
                                    @if (($fila['estado'] ?? null) === 11)
                                        ${{ number_format($fila['monto'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- MONTO --}}
                                <td>
                                    @if (($fila['estado'] ?? null) === 11)
                                        ${{ number_format($fila['monto'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- FECHA --}}
                                <td>
                                    @if (($fila['estado'] ?? null) === 11 && !empty($fila['fechaPago']))
                                        {{ \Carbon\Carbon::parse($fila['fechaPago'])->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- FORMA DE PAGO --}}
                                <td colspan="3">
                                    @if (($fila['estado'] ?? null) === 11)
                                        {{ $fila['formaPago'] ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- SALDO / ESTADO --}}
                                <td>
                                    {{ $saldo > 0 ? '$' . number_format($saldo, 2) : '-' }}
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="9" class="tablaVacia">
                                    No existen movimientos para este estudiante.
                                </td>
                            </tr>
                        @endforelse




                    </tbody>
                </table>



                <table class="tabla tabla-pequena tabla-secundaria">
                    <tbody class="tabla-cuerpo">

                        <tr class="tabla-encabezado">
                            <th>Concepto</th>
                            <th>#</th>
                            <th>Monto</th>
                        </tr>

                        <tr>
                            <td>Mensualidad</td>
                            <td>{{ $resumen['mensualidad']['cantidad'] }}</td>
                            <td>
                                {{ $resumen['mensualidad']['monto'] > 0
                                    ? '$' . number_format($resumen['mensualidad']['monto'], 2)
                                    : '-' }}
                            </td>
                        </tr>

                        <tr>
                            <td>Inscripción</td>
                            <td>{{ $resumen['inscripcion']['cantidad'] }}</td>
                            <td>
                                {{ $resumen['inscripcion']['monto'] > 0
                                    ? '$' . number_format($resumen['inscripcion']['monto'], 2)
                                    : '-' }}
                            </td>
                        </tr>

                        <tr>
                            <td>Recargo</td>
                            <td>{{ $resumen['recargo']['cantidad'] }}</td>
                            <td>
                                {{ $resumen['recargo']['monto'] > 0
                                    ? '$' . number_format($resumen['recargo']['monto'], 2)
                                    : '-' }}
                            </td>
                        </tr>

                        <tr>
                            <td>Examen</td>
                            <td>{{ $resumen['examen']['cantidad'] }}</td>
                            <td>
                                {{ $resumen['examen']['monto'] > 0
                                    ? '$' . number_format($resumen['examen']['monto'], 2)
                                    : '-' }}
                            </td>
                        </tr>

                        <tr>
                            <td>Uniforme</td>
                            <td>{{ $resumen['uniforme']['cantidad'] }}</td>
                            <td>
                                {{ $resumen['uniforme']['monto'] > 0
                                    ? '$' . number_format($resumen['uniforme']['monto'], 2)
                                    : '-' }}
                            </td>
                        </tr>

                        {{-- TOTAL PAGADO --}}
                        <tr>
                            <td><strong>Total pagado</strong></td>
                            <td><strong>{{ $totalCantidad }}</strong></td>
                            <td>
                                <strong>
                                    {{ $totalPagado > 0
                                        ? '$' . number_format($totalPagado, 2)
                                        : '-' }}
                                </strong>
                            </td>
                        </tr>

                    </tbody>
                </table>






            </section>

        @else


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
                            <th>Monto</th>
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

                                    <td>${{ number_format($pago->montoAPagar, 2) }}</td>

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

        @endif

    </main>

</body>
</html>
