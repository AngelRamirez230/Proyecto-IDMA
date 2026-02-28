<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>

<body style="font-family:Arial; font-size:11px;">

<!-- ================= TITULO ================= -->

<table>
    <colgroup>
        <col style="width:1200px;">
    </colgroup>
    <tr>
        <td style="text-align:center; font-size:16px; font-weight:bold;">
            ESTADO DE CUENTA
        </td>
    </tr>
    <tr>
        <td style="text-align:center;">
            {{ $ciclo['nombreCiclo'] ?? '-' }}
        </td>
    </tr>
</table>

<br>

<!-- ================= DATOS + RESUMEN ================= -->

<table>
    <colgroup>
        <col style="width:750px;">
        <col style="width:50px;">
        <col style="width:400px;">
    </colgroup>
    <tr>

        <!-- IZQUIERDA -->
        <td valign="top">

            <table>
                <colgroup>
                    <col style="width:150px;">
                    <col style="width:200px;">
                    <col style="width:150px;">
                    <col style="width:250px;">
                </colgroup>

                <tr>
                    <td colspan="4" style="text-align:center; font-weight:bold; background-color:#79272C; color:white;">
                        DATOS GENERALES DEL ESTUDIANTE
                    </td>
                </tr>

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
                        {{ mb_strtoupper(
                            $estudiante->planDeEstudios->licenciatura->nombreLicenciatura ?? '-',
                            'UTF-8'
                        ) }}
                    </td>
                </tr>

                <tr>
                    <td><strong>Matrícula</strong></td>
                    <td>{{ $estudiante->matriculaAlfanumerica }}</td>
                    <td><strong>Generación</strong></td>
                    <td>{{ $estudiante->generacion->nombreGeneracion ?? '-' }}</td>
                </tr>

            </table>

        </td>

        <td></td>

        <!-- DERECHA -->
        <td valign="top">

            <table>
                <colgroup>
                    <col style="width:250px;">
                    <col style="width:150px;">
                </colgroup>

                <tr>
                    <td colspan="2" style="text-align:center; font-weight:bold; background-color:#79272C; color:white;">
                        RESUMEN DE LA CUENTA
                    </td>
                </tr>

                <tr><td>Importe total</td><td>{{ number_format($ciclo['importeTotal'],2,'.','') }}</td></tr>
                <tr><td>Becas(-)</td><td>{{ number_format($ciclo['becasTotal'],2,'.','') }}</td></tr>
                <tr><td>Descuentos</td><td>{{ number_format($ciclo['descuentosTotal'] ?? 0,2,'.','') }}</td></tr>
                <tr><td>Saldo a pagar</td><td>{{ number_format($ciclo['saldoAPagar'] ?? 0,2,'.','') }}</td></tr>
                <tr><td>Abonos a saldo(-)</td><td>{{ number_format($ciclo['abonosASaldo'] ?? 0,2,'.','') }}</td></tr>
                <tr><td>Abono a recargos</td><td>{{ number_format($ciclo['abonoARecargos'] ?? 0,2,'.','') }}</td></tr>
                <tr><td>Saldo pendiente</td><td>{{ number_format($ciclo['saldoPendiente'] ?? 0,2,'.','') }}</td></tr>
                <tr><td>Saldo vencido</td><td>{{ number_format($ciclo['saldoVencido'] ?? 0,2,'.','') }}</td></tr>
                <tr><td>Recargos(+)</td><td>{{ number_format($ciclo['recargosTotal'] ?? 0,2,'.','') }}</td></tr>
                <tr>
                    <td><strong>Saldo actual</strong></td>
                    <td><strong>{{ number_format($ciclo['saldoActual'] ?? 0,2,'.','') }}</strong></td>
                </tr>

            </table>

        </td>

    </tr>
</table>

<br>

<!-- ================= DETALLE DE MOVIMIENTOS ================= -->

<!-- ================= PAGOS NO PAGADOS ================= -->

<table>
    <colgroup>
        <col style="width:120px;">
        <col style="width:200px;">
        <col style="width:120px;">
        <col style="width:100px;">
        <col style="width:90px;">
        <col style="width:90px;">
        <col style="width:90px;">
        <col style="width:100px;">
        <col style="width:110px;">
    </colgroup>

    <tr>
        <td colspan="9" style="text-align:center; font-weight:bold; background-color:#79272C; color:white;">
            PAGOS NO PAGADOS
        </td>
    </tr>

    <tr style="background-color:#79272C; color:white;">
        <td style="text-align:center; font-weight:bold;">Referencia</td>
        <td style="text-align:center; font-weight:bold;">Concepto</td>
        <td style="text-align:center; font-weight:bold;">Fecha límite</td>
        <td style="text-align:center; font-weight:bold;">Importe</td>
        <td style="text-align:center; font-weight:bold;">Beca</td>
        <td style="text-align:center; font-weight:bold;">Descuento</td>
        <td style="text-align:center; font-weight:bold;">Recargo</td>
        <td style="text-align:center; font-weight:bold;">Total</td>
        <td style="text-align:center; font-weight:bold;">Ref. Original</td>
    </tr>

    @forelse ($ciclo['pagosNoPagados'] as $pago)
    <tr>
        <td>{{ $pago->Referencia }}</td>
        <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
        <td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
        <td>{{ number_format($pago->costo_concepto_mostrar,2,'.','') }}</td>
        <td>{{ number_format($pago->descuentoDeBeca,2,'.','') }}</td>
        <td>{{ number_format($pago->descuentoDePago,2,'.','') }}</td>
        <td>{{ number_format($pago->recargo_concepto,2,'.','') }}</td>
        <td>{{ number_format($pago->montoAPagar,2,'.','') }}</td>
        <td>{{ $pago->referenciaOriginal ?? '-' }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="9" style="text-align:center;">No hay pagos no pagados.</td>
    </tr>
    @endforelse
</table>

<br>

<!-- ================= PAGOS PENDIENTES ================= -->

<table>
    <colgroup>
        <col style="width:120px;">
        <col style="width:200px;">
        <col style="width:120px;">
        <col style="width:100px;">
        <col style="width:90px;">
        <col style="width:90px;">
        <col style="width:90px;">
        <col style="width:100px;">
        <col style="width:110px;">
    </colgroup>

    <tr>
        <td colspan="9" style="text-align:center; font-weight:bold; background-color:#79272C; color:white;">
            PAGOS PENDIENTES
        </td>
    </tr>

    <tr style="background-color:#79272C; color:white;">
        <td style="font-weight:bold;">Referencia</td>
        <td style="font-weight:bold;">Concepto</td>
        <td style="font-weight:bold;">Fecha límite</td>
        <td style="font-weight:bold;">Importe</td>
        <td style="font-weight:bold;">Beca</td>
        <td style="font-weight:bold;">Descuento</td>
        <td style="font-weight:bold;">Recargo</td>
        <td style="font-weight:bold;">Total</td>
        <td style="font-weight:bold;">Ref. Original</td>
    </tr>

    @forelse ($ciclo['pagosPendientes'] as $pago)
    <tr>
        <td>{{ $pago->Referencia }}</td>
        <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
        <td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
        <td>{{ number_format($pago->costo_concepto_mostrar,2,'.','') }}</td>
        <td>{{ number_format($pago->descuentoDeBeca,2,'.','') }}</td>
        <td>{{ number_format($pago->descuentoDePago,2,'.','') }}</td>
        <td>{{ number_format($pago->recargo_concepto,2,'.','') }}</td>
        <td>{{ number_format($pago->montoAPagar,2,'.','') }}</td>
        <td>{{ $pago->referenciaOriginal ?? '-' }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="9" style="text-align:center;">No hay pagos pendientes.</td>
    </tr>
    @endforelse
</table>

<br>

<!-- ================= PAGOS APROBADOS ================= -->

<table>
    <colgroup>
        <col style="width:120px;">
        <col style="width:200px;">
        <col style="width:100px;">
        <col style="width:120px;">
        <col style="width:120px;">
        <col style="width:100px;">
        <col style="width:120px;">
        <col style="width:120px;">
    </colgroup>

    <tr>
        <td colspan="8" style="text-align:center; font-weight:bold; background-color:#79272C; color:white;">
            PAGOS APROBADOS
        </td>
    </tr>

    <tr style="background-color:#79272C; color:white;">
        <td style="font-weight:bold;">Referencia</td>
        <td style="font-weight:bold;">Concepto</td>
        <td style="font-weight:bold;">Aportación</td>
        <td style="font-weight:bold;">Fecha pago</td>
        <td style="font-weight:bold;">Método</td>
        <td style="font-weight:bold;">Abono saldo</td>
        <td style="font-weight:bold;">Abono recargos</td>
        <td style="font-weight:bold;">Total</td>
    </tr>

    @forelse ($ciclo['pagosAprobados'] as $pago)
    <tr>
        <td>{{ $pago->Referencia }}</td>
        <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
        <td>{{ $pago->aportacion ?? '-' }}</td>
        <td>{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
        <td>{{ $pago->idTipoDePago == 1 ? 'Efectivo' : ($pago->idTipoDePago == 3 ? 'Transferencia' : '-') }}</td>
        <td>{{ number_format($pago->abono_saldo,2,'.','') }}</td>
        <td>{{ number_format($pago->abono_recargo,2,'.','') }}</td>
        <td>{{ number_format($pago->montoAPagar,2,'.','') }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="8" style="text-align:center;">No hay pagos aprobados.</td>
    </tr>
    @endforelse
</table>

<br>

<!-- ================= OTROS PAGOS ================= -->

<table>
    <colgroup>
        <col style="width:120px;">
        <col style="width:200px;">
        <col style="width:120px;">
        <col style="width:120px;">
        <col style="width:120px;">
        <col style="width:120px;">
        <col style="width:120px;">
    </colgroup>

    <tr>
        <td colspan="7" style="text-align:center; font-weight:bold; background-color:#79272C; color:white;">
            OTROS PAGOS
        </td>
    </tr>

    <tr style="background-color:#79272C; color:white;">
        <td style="font-weight:bold;">Referencia</td>
        <td style="font-weight:bold;">Concepto</td>
        <td style="font-weight:bold;">Fecha límite</td>
        <td style="font-weight:bold;">Fecha pago</td>
        <td style="font-weight:bold;">Método</td>
        <td style="font-weight:bold;">Total</td>
        <td style="font-weight:bold;">Estatus</td>
    </tr>

    @forelse ($ciclo['otrosPagos'] as $pago)
    <tr>
        <td>{{ $pago->Referencia }}</td>
        <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
        <td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
        <td>{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
        <td>{{ $pago->idTipoDePago == 1 ? 'Efectivo' : ($pago->idTipoDePago == 3 ? 'Transferencia' : '-') }}</td>
        <td>{{ number_format($pago->montoAPagar,2,'.','') }}</td>
        <td>{{ $pago->estatus->nombreTipoDeEstatus ?? '-' }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="7" style="text-align:center;">No hay otros pagos.</td>
    </tr>
    @endforelse
</table>

</body>
</html>