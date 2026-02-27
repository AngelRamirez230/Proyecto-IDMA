<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>
        Estado de Cuenta - {{ $ciclo['nombreCiclo'] ?? '' }}
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        h1 {
            text-align: center;
            color: #79272C;
            margin-bottom: 8px;
        }

        h2 {
            text-align: center;
            margin-top: 18px;
            margin-bottom: 5px;
            color: #79272C;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #5B5B5B;
            padding: 4px;
            text-align: center;
            word-wrap: break-word;
        }

        .contenedor-superior td {
            border: none !important;
        }

        .encabezado-tabla th,
        .encabezado-columnas th {
            background-color: #79272C;
            color: white;
        }

        th {
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .tablaVacia {
            text-align: center;
            font-weight: bold;
            color: #79272C;
        }

        .resumen td:first-child {
            text-align: left;
        }

        .resumen td:last-child {
            text-align: right;
        }
    </style>
</head>
<body>

<h1>ESTADO DE CUENTA</h1>
<h2 style="text-align:center; margin-top:0;">
    {{ $ciclo['nombreCiclo'] ?? '-' }}
</h2>

<!-- ================= CONTENEDOR DOS COLUMNAS ================= -->

<table width="100%" class="contenedor-superior" style="margin-bottom:15px;">
    <tr>
        <!-- IZQUIERDA -->
        <td width="60%" valign="top">

            <table>
                <thead>
                    <tr>
                        <th colspan="4" style="background-color:#79272C; color:white;">
                            DATOS GENERALES DEL ESTUDIANTE
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Nombre</strong></td>
                        <td colspan="3">
                            {{ strtoupper(
                                $estudiante->usuario->primerNombre.' '.
                                $estudiante->usuario->segundoNombre.' '.
                                $estudiante->usuario->primerApellido.' '.
                                $estudiante->usuario->segundoApellido
                            ) }}
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Carrera</strong></td>
                        <td colspan="3">
                            {{ mb_strtoupper($estudiante->planDeEstudios->licenciatura->nombreLicenciatura ?? '-', 'UTF-8') }}
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Matrícula</strong></td>
                        <td>{{ $estudiante->matriculaAlfanumerica }}</td>
                        <td><strong>Generación</strong></td>
                        <td>{{ $estudiante->generacion->nombreGeneracion ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>

        </td>

        <!-- ESPACIO -->
        <td width="10%"></td>

        <!-- DERECHA -->
        <td width="30%" valign="top">

            <table class="resumen">
                <thead>
                    <tr>
                        <th colspan="2" style="background-color:#79272C; color:white;">
                            RESUMEN DE LA CUENTA
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Importe total</td><td>${{ number_format($ciclo['importeTotal'], 2) }}</td></tr>
                    <tr><td>Becas(-)</td><td>${{ number_format($ciclo['becasTotal'], 2) }}</td></tr>
                    <tr><td>Descuentos</td><td>${{ number_format($ciclo['descuentosTotal'] ?? 0, 2) }}</td></tr>
                    <tr><td>Saldo a pagar</td><td>${{ number_format($ciclo['saldoAPagar'] ?? 0, 2) }}</td></tr>
                    <tr><td>Abonos a saldo(-)</td><td>${{ number_format($ciclo['abonosASaldo'] ?? 0, 2) }}</td></tr>
                    <tr><td>Abono a recargos</td><td>${{ number_format($ciclo['abonoARecargos'] ?? 0, 2) }}</td></tr>
                    <tr><td>Saldo pendiente</td><td>${{ number_format($ciclo['saldoPendiente'] ?? 0, 2) }}</td></tr>
                    <tr><td>Saldo vencido</td><td>${{ number_format($ciclo['saldoVencido'] ?? 0, 2) }}</td></tr>
                    <tr><td>Recargos(+)</td><td>${{ number_format($ciclo['recargosTotal'] ?? 0, 2) }}</td></tr>
                    <tr>
                        <td><strong>Saldo actual</strong></td>
                        <td><strong>${{ number_format($ciclo['saldoActual'] ?? 0, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

        </td>
    </tr>
</table>

<h2>DETALLE DE MOVIMIENTOS</h2>

<!-- ================= PAGOS NO PAGADOS ================= -->

<table>
    <tbody>
        <tr class="encabezado-tabla">
            <th colspan="9">PAGOS NO PAGADOS</th>
        </tr>
        <tr class="encabezado-columnas">
            <th>Referencia</th>
            <th>Concepto</th>
            <th>Fecha límite</th>
            <th>Importe</th>
            <th>Beca</th>
            <th>Descuento</th>
            <th>Recargo</th>
            <th>Total</th>
            <th>Ref. Original</th>
        </tr>

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
                <td>{{ $pago->referenciaOriginal ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="tablaVacia">No hay pagos no pagados.</td>
            </tr>
        @endforelse
    </tbody>
</table>


<!-- ================= PAGOS PENDIENTES ================= -->

<table>
    <tbody>
        <tr class="encabezado-tabla">
            <th colspan="9">PAGOS PENDIENTES</th>
        </tr>
        <tr class="encabezado-columnas">
            <th>Referencia</th>
            <th>Concepto</th>
            <th>Fecha límite</th>
            <th>Importe</th>
            <th>Beca</th>
            <th>Descuento</th>
            <th>Recargo</th>
            <th>Total</th>
            <th>Ref. Original</th>
        </tr>

        @forelse ($ciclo['pagosPendientes'] as $pago)
            <tr>
                <td>{{ $pago->Referencia }}</td>
                <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
                <td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
                <td>${{ number_format($pago->costo_concepto_mostrar, 2) }}</td>
                <td>${{ number_format($pago->descuentoDeBeca, 2) }}</td>
                <td>${{ number_format($pago->descuentoDePago, 2) }}</td>
                <td>${{ number_format($pago->recargo_concepto, 2) }}</td>
                <td>${{ number_format($pago->montoAPagar, 2) }}</td>
                <td>{{ $pago->referenciaOriginal ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="tablaVacia">No hay pagos pendientes.</td>
            </tr>
        @endforelse
    </tbody>
</table>


<!-- ================= PAGOS APROBADOS ================= -->

<table>
    <tbody>
        <tr class="encabezado-tabla">
            <th colspan="8">PAGOS APROBADOS</th>
        </tr>
        <tr class="encabezado-columnas">
            <th>Referencia</th>
            <th>Concepto</th>
            <th>Aportación</th>
            <th>Fecha pago</th>
            <th>Método</th>
            <th>Abono saldo</th>
            <th>Abono recargos</th>
            <th>Total</th>
        </tr>

        @forelse ($ciclo['pagosAprobados'] as $pago)
            <tr>
                <td>{{ $pago->Referencia }}</td>
                <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
                <td>{{ $pago->aportacion ?? '-' }}</td>
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


<!-- ================= OTROS PAGOS ================= -->

<table>
    <tbody>
        <tr class="encabezado-tabla">
            <th colspan="7">OTROS PAGOS</th>
        </tr>
        <tr class="encabezado-columnas">
            <th>Referencia</th>
            <th>Concepto</th>
            <th>Fecha límite</th>
            <th>Fecha pago</th>
            <th>Método</th>
            <th>Total</th>
            <th>Estatus</th>
        </tr>

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
                <td colspan="7" class="tablaVacia">No hay otros pagos.</td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>