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

        <div class="form-group2 acciones-reporte">

            <a href="{{ route('estadosCuenta.seleccionarEstudiante') }}"
            class="btn-boton-formulario2 btn-cancelar2">
                Cancelar
            </a>

        </div>


        @foreach ($estadoCuentaPorCiclo as $idCiclo => $ciclo)

            <div class="bloque-ciclo">

            <button class="btn-ciclo"
                onclick="document.getElementById('ciclo-{{ $idCiclo }}').classList.toggle('oculto')">
                CICLO: 
                {{ strtoupper($ciclo['nombreCiclo']) }}
            </button>

                <div id="ciclo-{{ $idCiclo }}" class="contenido-ciclo oculto">

                    <!-- =========================
                        BOTONES DE EXPORTACIÓN
                    ========================== -->
                    <div class="form-group2 acciones-reporte">

                        <form action="{{ route('reportes.pdf') }}" method="POST">
                            @csrf
                            <button class="btn-boton-formulario2">Exportar PDF</button>
                        </form>

                        <form action="{{ route('reportes.excel') }}" method="POST">
                            @csrf
                            <button class="btn-boton-formulario2">Exportar Excel</button>
                        </form>

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
                                        <td colspan="6">${{ number_format($ciclo['importeTotal'], 2) }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Becas(-)</strong></td>
                                        <td colspan="6">${{ number_format($ciclo['becasTotal'], 2) }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Descuentos</strong></td>
                                        <td colspan="6">${{ number_format($ciclo['descuentosTotal'] ?? 0, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Saldo a pagar</strong></td>
                                        <td colspan="6">${{ number_format($ciclo['saldoAPagar'] ?? 0, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Abonos a saldo(-)</strong></td>
                                        <td colspan="6">${{ number_format($ciclo['abonosASaldo'] ?? 0, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Abono a recargos</strong></td>
                                        <td colspan="6">${{ number_format($ciclo['abonoARecargos'] ?? 0, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Saldo pendiente</strong></td>
                                        <td colspan="6">${{ number_format($ciclo['saldoPendiente'] ?? 0, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Saldo vencido</strong></td>
                                        <td colspan="6">${{ number_format($ciclo['saldoVencido'] ?? 0, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Recargos(+)</strong></td>
                                        <td colspan="6">${{ number_format($ciclo['recargosTotal'] ?? 0, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Saldo actual</strong></td>
                                        <td colspan="6">${{ number_format($ciclo['saldoActual'] ?? 0, 2) }}</td>
                                    </tr>

                                </tbody>
                            </table>

                        </div>
                    </section>


                    <h1 class="titulo-form2 titulo-centrado" >DETALLES DE MOVIMIENTOS</h1>

                    <section class="consulta-tabla-contenedor">
                        

                        <table class="tabla">
                            <thead>
                                <tr class="tabla-encabezado">
                                    <th colspan="9" >NO PAGADOS</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr class="tabla-encabezado">
                                    <th>Referencia</th>
                                    <th>Concepto</th>
                                    <th>Fecha límite</th>
                                    <th>Importe total</th>
                                    <th>Beca (-)</th>
                                    <th>Descuento (-)</th>
                                    <th>Recargo (+)</th>
                                    <th>Total a pagar</th>
                                    <th>Referencia original</th>
                                </tr>
                            </thead>

                            <tbody class="tabla-cuerpo">
                                @forelse ($ciclo['pagosNoPagados'] as $pago)
                                    <tr>
                                        <td>{{ $pago->Referencia }}</td>
                                        <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
                                        <td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
                                        <td>${{ number_format($pago->costo_concepto_mostrar, 2) }}</td>
                                        <td>${{ number_format($pago->descuentoDeBeca, 2) }}</td>
                                        <td>${{ number_format($pago->descuentoDePago, 2) }}</td>
                                        <td>${{ number_format($pago->recargo_concepto, 2) }}</td>
                                        <td>${{ number_format($pago->montoAPagar, 2) }}</td>
                                        <td>{{ $pago->referenciaOriginal ?? '-'}}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="tablaVacia">No hay pagos no pagados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </section>

                    <section class="consulta-tabla-contenedor">

                        <table class="tabla">

                            <thead>
                                <tr class="tabla-encabezado">
                                    <th colspan="9" >PAGOS PENDIENTES</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr class="tabla-encabezado">
                                    <th>Referencia</th>
                                    <th>Concepto</th>
                                    <th>Fecha límite</th>
                                    <th>Importe total</th>
                                    <th>Beca (-)</th>
                                    <th>Descuento (-)</th>
                                    <th>Recargo (+)</th>
                                    <th>Total a pagar</th>
                                    <th>Referencia original</th>
                                </tr>
                            </thead>

                            <tbody class="tabla-cuerpo">
                                @forelse ($ciclo['pagosPendientes'] as $pago)
                                    <tr>
                                        <td>{{ $pago->Referencia }}</td>
                                        <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
                                        <td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
                                        <td>${{ number_format($pago->costo_concepto_mostrar, 2) }}</td>
                                        <td>${{  number_format($pago->descuentoDeBeca, 2) }}</td>
                                        <td>${{  number_format($pago->descuentoDePago, 2) }}</td>
                                        <td>${{ number_format($pago->recargo_concepto, 2) }}</td>
                                        <td>${{ number_format($pago->montoAPagar, 2) }}</td>
                                        <td>{{ $pago->referenciaOriginal ?? '-'}}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="tablaVacia">No hay pagos pendientes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </section>


                    <section class="consulta-tabla-contenedor">

                        <table class="tabla">
                            <thead>
                                <tr class="tabla-encabezado">
                                    <th colspan="8" >PAGOS APROBADOS</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr class="tabla-encabezado">
                                    <th>Referencia</th>
                                    <th>Concepto</th>
                                    <th>Aportación</th>
                                    <th>Fecha de pago</th>
                                    <th>Método de pago</th>
                                    <th>Abono a saldo</th>
                                    <th>Abono a recargos</th>
                                    <th>Total</th>
                                </tr>
                            </thead>

                            <tbody class="tabla-cuerpo">
                                @forelse ($ciclo['pagosAprobados'] as $pago)
                                    <tr>
                                        <td>{{ $pago->Referencia }}</td>
                                        <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
                                        <td>{{ $pago->aportacion ?? $pago->concepto->nombreConceptoDePago }}</td>
                                        <td>{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
                                        <td>
                                            {{
                                                $pago->idTipoDePago == 1 ? 'Efectivo' :
                                                ($pago->idTipoDePago == 3 ? 'Transferencia' : '-')
                                            }}
                                        </td>
                                        <td>${{ number_format($pago->abono_saldo, 2) }}</td>
                                        <td>${{ number_format($pago->abono_recargo, 2) }}</td>
                                        <td>${{ number_format($pago->montoAPagar, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="tablaVacia">No hay pagos aprobados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </section>



                    <section class="consulta-tabla-contenedor">

                    <table class="tabla">
                        <thead>
                            <tr class="tabla-encabezado">
                                <th colspan="7">OTROS PAGOS</th>
                            </tr>
                        </thead>
                        <thead>
                            <tr class="tabla-encabezado">
                                <th>Referencia</th>
                                <th>Concepto</th>
                                <th>Fecha límite</th>
                                <th>Fecha de pago</th>
                                <th>Método de pago</th>
                                <th>Total</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>

                        <tbody class="tabla-cuerpo">
                            @forelse ($ciclo['otrosPagos'] as $pago)
                                <tr>
                                    <td>{{ $pago->Referencia }}</td>

                                    <td>{{ $pago->concepto->nombreConceptoDePago }}</td>

                                    <td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>

                                    <td>{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>

                                    <td>
                                        {{
                                            $pago->idTipoDePago == 1 ? 'Efectivo' :
                                            ($pago->idTipoDePago == 3 ? 'Transferencia' : '-')
                                        }}
                                    </td>
                                    

                                    <td>${{ number_format($pago->montoAPagar, 2) }}</td>

                                    <td>{{ $pago->estatus->nombreTipoDeEstatus ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="tablaVacia">
                                        No hay otros pagos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </section>


                </div>
            </div>

        @endforeach

    </main>
    
</body>
</html>