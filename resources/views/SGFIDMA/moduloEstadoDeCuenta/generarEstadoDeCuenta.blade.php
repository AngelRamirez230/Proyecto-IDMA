<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de cuenta</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="consulta">

        <h1 class="titulo-form2">Estado de cuenta</h1>

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

            <a href="{{ route('estadosCuenta.seleccionarEstudiante') }}"
               class="btn-boton-formulario2 btn-cancelar2">
                Cancelar
            </a>

        </div>



        <section class="consulta-tabla-contenedor">
            <div class="estado-cuenta-tablas">


                <table class="tabla tabla-pequena2 tabla-secundaria">
                    <tbody>
                        <thead>
                            <tr class="tabla-encabezado">
                                <th colspan="9"><strong>DATOS GENERALES DEL ESTUDIANTE</strong></th>
                            </tr>
                        </thead>

                        <tr>
                            <td><strong>Nombre:</strong></td>
                            <td colspan="8">
                                {{ Str::upper(
                                    $estudiante->usuario->primerNombre.' '.
                                    $estudiante->usuario->segundoNombre.' '.
                                    $estudiante->usuario->primerApellido.' '.
                                    $estudiante->usuario->segundoApellido
                                ) }}
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Carrera:</strong></td>
                            <td colspan="8">
                                {{ mb_strtoupper($estudiante->planDeEstudios->licenciatura->nombreLicenciatura ?? '-', 'UTF-8') }}
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Matrícula</strong></td>
                            <td>{{ $estudiante->matriculaAlfanumerica }}</td>
                            <td><strong>Generación:</strong></td>
                            <td colspan="6">{{ $estudiante->generacion->nombreGeneracion ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>


                <table class="tabla tabla-pequena2 tabla-secundaria2">
                    <tbody>
                        <thead>
                            <tr class="tabla-encabezado">
                                <th colspan="9"><strong>RESUMEN DE LA CUENTA</strong></th>
                            </tr>
                        </thead>


                        <tr>
                            <td><strong>Importe total</strong></td>
                            <td colspan="6">${{ number_format($importeTotal, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Becas(-)</strong></td>
                            <td colspan="6">${{ number_format($becasTotal, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Descuentos</strong></td>
                            <td colspan="6">${{ number_format($descuentosTotal ?? 0, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Saldo a pagar</strong></td>
                            <td colspan="6">${{ number_format($saldoAPagar ?? 0, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Abonos a saldo(-)</strong></td>
                            <td colspan="6">${{ number_format($abonosASaldo ?? 0, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Abono a recargos</strong></td>
                            <td colspan="6">${{ number_format($abonoARecargos ?? 0, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Saldo pendiente</strong></td>
                            <td colspan="6">${{ number_format($saldoPendiente ?? 0, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Saldo vencido</strong></td>
                            <td colspan="6">${{ number_format($saldoVencido ?? 0, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Recargos(+)</strong></td>
                            <td colspan="6">${{ number_format($recargosTotal ?? 0, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Saldo actual</strong></td>
                            <td colspan="6">${{ number_format($saldoActual ?? 0, 2) }}</td>
                        </tr>

                    </tbody>
                </table>

            </div>
        </section>


        <h1 class="titulo-form2 titulo-centrado" >DETALLES DE MOVIMIENTOS</h1>

        <section class="consulta-tabla-contenedor">
            <h3 class="consulta-subtitulo">Pagos pendientes</h3>

            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Referencia</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Fecha límite</th>
                        <th>Fecha de pago</th>
                        <th>Estatus</th>
                    </tr>
                </thead>

                <tbody class="tabla-cuerpo">
                    @forelse ($pagosPendientes as $pago)
                        <tr>
                            <td>{{ $pago->Referencia }}</td>
                            <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
                            <td>${{ number_format($pago->montoAPagar, 2) }}</td>
                            <td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $pago->estatus->nombreTipoDeEstatus }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="tablaVacia">No hay pagos pendientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>


        <section class="consulta-tabla-contenedor">
            <h3 class="consulta-subtitulo">Pagos aprobados</h3>

            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Referencia</th>
                        <th>Concepto</th>
                        <th>Aportación</th>
                        <th>Monto</th>
                        <th>Fecha límite</th>
                        <th>Fecha de pago</th>
                        <th>Estatus</th>
                    </tr>
                </thead>

                <tbody class="tabla-cuerpo">
                    @forelse ($pagosAprobados as $pago)
                        <tr>
                            <td>{{ $pago->Referencia }}</td>
                            <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
                            <td>{{ $pago->aportacion ?? $pago->concepto->nombreConceptoDePago }}</td>
                            <td>${{ number_format($pago->montoAPagar, 2) }}</td>
                            <td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $pago->estatus->nombreTipoDeEstatus }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="tablaVacia">No hay pagos aprobados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>


        <section class="consulta-tabla-contenedor">
            <h3 class="consulta-subtitulo">No pagados</h3>

            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Referencia</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Fecha límite</th>
                        <th>Fecha de pago</th>
                        <th>Estatus</th>
                    </tr>
                </thead>

                <tbody class="tabla-cuerpo">
                    @forelse ($pagosNoPagados as $pago)
                        <tr>
                            <td>{{ $pago->Referencia }}</td>
                            <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
                            <td>${{ number_format($pago->montoAPagar, 2) }}</td>
                            <td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $pago->estatus->nombreTipoDeEstatus }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="tablaVacia">No hay pagos no pagados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>



    </main>
    
</body>
</html>