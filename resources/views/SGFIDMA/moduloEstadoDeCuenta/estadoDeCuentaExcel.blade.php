<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>

<body>

    <!-- ================= TITULO ================= -->

    <table cellspacing="0" cellpadding="4">
        <colgroup>
            <col style="width:300px;"> <!-- A -->
            <col style="width:300px;"> <!-- B -->
            <col style="width:180px;"> <!-- C -->
            <col style="width:200px;"> <!-- D -->
            <col style="width:180px;">  <!-- E -->
            <col style="width:180px;">  <!-- F -->
            <col style="width:180px;">  <!-- G -->
            <col style="width:180px;"> <!-- H -->
            <col style="width:300px;"> <!-- I -->
        </colgroup>

        <tr>
            <td colspan="9" align="center">
                <b>ESTADO DE CUENTA</b>
            </td>
        </tr>
        <tr>
            <td colspan="9" align="center">
                <b>{{ $ciclo['nombreCiclo'] ?? '-' }}</b>
            </td>
        </tr>
    </table>

    <br>

    <!-- ================= CONTENEDOR SUPERIOR ================= -->

    <table cellspacing="0" cellpadding="4">

        <colgroup>
            <col style="width:300px;"> <!-- A -->
            <col style="width:300px;"> <!-- B -->
            <col style="width:180px;"> <!-- C -->
            <col style="width:200px;"> <!-- D -->
            <col style="width:180px;">  <!-- E -->
            <col style="width:180px;">  <!-- F -->
            <col style="width:180px;">  <!-- G -->
            <col style="width:180px;"> <!-- H -->
            <col style="width:300px;"> <!-- I -->
        </colgroup>

        <!-- ================= FILA 1 : TITULOS ================= -->

        <tr>

            <!-- A-D -->
            <td colspan="4" align="center">
                <b>DATOS GENERALES DEL ESTUDIANTE</b>
            </td>

            <!-- E-F-G separación -->
            <td></td>
            <td></td>
            <td></td>

            <!-- H-I -->
            <td colspan="2" align="center">
                <b>RESUMEN DE LA CUENTA</b>
            </td>

        </tr>


        <!-- ================= FILA 2 ================= -->

        <tr>

            <!-- A -->
            <td align="center"> <b>Nombre:</b></td>

            <!-- B-C-D -->
            <td align="center" colspan="3">
                {{ strtoupper(
                    $estudiante->usuario->primerNombre.' '.
                    $estudiante->usuario->segundoNombre.' '.
                    $estudiante->usuario->primerApellido.' '.
                    $estudiante->usuario->segundoApellido
                ) ?? '-' }}
            </td>

            <!-- E-F-G -->
            <td></td>
            <td></td>
            <td></td>

            <!-- H -->
            <td><b>Importe total</b></td>

            <!-- I -->
            <td align="right">{{ $ciclo['importeTotal'] ?? 0 }}</td>

        </tr>


        <!-- ================= FILA 3 ================= -->

        <tr>

            <td align="center"><b>Carrera:</b></td>
            <td align="center" colspan="3">
                {{ mb_strtoupper(
                    $estudiante->planDeEstudios->licenciatura->nombreLicenciatura ?? '-',
                    'UTF-8'
                ) ?? '-' }}
            </td>

            <td></td>
            <td></td>
            <td></td>

            <td><b>Becas(-)</b></td>
            <td align="right">{{ $ciclo['becasTotal'] ?? 0 }}</td>

        </tr>


        <!-- ================= FILA 4 ================= -->

        <tr>

            <td align="center"><b>Matrícula:</b></td>
            <td align="center">{{ $estudiante->matriculaAlfanumerica }}</td>
            <td align="center"><b>Generación:</b></td>
            <td align="center">{{ $estudiante->generacion->nombreGeneracion ?? '-' }}</td>

            <td></td>
            <td></td>
            <td></td>

            <td><b>Descuentos</b></td>
            <td align="right">{{ $ciclo['descuentosTotal'] ?? 0}}</td>

        </tr>


        <!-- ================= FILA 5 ================= -->

        <tr>

            <td></td>
            <td></td>
            <td></td>
            <td></td>

            <td></td>
            <td></td>
            <td></td>

            <td><b>Saldo a pagar</b></td>
            <td align="right">{{ $ciclo['saldoAPagar'] ?? 0 }}</td>

        </tr>


        <!-- ================= FILA 6 ================= -->

        <tr>

            <td></td>
            <td></td>
            <td></td>
            <td></td>

            <td></td>
            <td></td>
            <td></td>

            <td><b>Abonos a saldo(-)</b></td>
            <td align="right">{{ $ciclo['abonosASaldo'] ?? 0 }}</td>

        </tr>


        <!-- ================= FILA 7 ================= -->

        <tr>

            <td></td>
            <td></td>
            <td></td>
            <td></td>

            <td></td>
            <td></td>
            <td></td>

            <td><b>Abono a recargos</b></td>
            <td align="right">{{ $ciclo['abonoARecargos'] ?? 0 }}</td>

        </tr>


        <!-- ================= FILA 8 ================= -->

        <tr>

            <td></td>
            <td></td>
            <td></td>
            <td></td>

            <td></td>
            <td></td>
            <td></td>

            <td><b>Saldo pendiente</b></td>
            <td align="right">{{ $ciclo['saldoPendiente'] ?? 0 }}</td>

        </tr>


        <!-- ================= FILA 9 ================= -->

        <tr>

            <td></td>
            <td></td>
            <td></td>
            <td></td>

            <td></td>
            <td></td>
            <td></td>

            <td><b>Saldo vencido</b></td>
            <td align="right">{{ $ciclo['saldoVencido'] ?? 0 }}</td>

        </tr>


        <!-- ================= FILA 10 ================= -->

        <tr>

            <td></td>
            <td></td>
            <td></td>
            <td></td>

            <td></td>
            <td></td>
            <td></td>

            <td><b>Recargos(+)</b></td>
            <td align="right">{{ $ciclo['recargosTotal'] ?? 0 }}</td>

        </tr>


        <!-- ================= FILA 11 ================= -->

        <tr>

            <td></td>
            <td></td>
            <td></td>
            <td></td>

            <td></td>
            <td></td>
            <td></td>

            <td><b>Saldo actual</b></td>
            <td align="right">{{ $ciclo['saldoActual'] ?? 0 }}</td>

        </tr>

    </table>

    <br>

    <!-- ================= PAGOS NO PAGADOS ================= -->

    <table>

        <colgroup>
            <col style="width:300px;"> <!-- A -->
            <col style="width:300px;"> <!-- B -->
            <col style="width:180px;"> <!-- C -->
            <col style="width:200px;"> <!-- D -->
            <col style="width:180px;">  <!-- E -->
            <col style="width:180px;">  <!-- F -->
            <col style="width:180px;">  <!-- G -->
            <col style="width:180px;"> <!-- H -->
            <col style="width:300px;"> <!-- I -->
        </colgroup>

        <tr>
            <td colspan="9" align="center"><b>PAGOS NO PAGADOS</b></td>
        </tr>
        <tr>
            <th><b>Referencia</b></th>
            <th><b>Concepto</b></th>
            <th><b>Fecha límite</b></th>
            <th><b>Importe total</b></th>
            <th><b>Beca (-)</b></th>
            <th><b>Descuento (-)</b></th>
            <th><b>Recargo (+)</b></th>
            <th><b>Total a pagar</b></th>
            <th><b>Referencia original</b></th>
        </tr>

        @forelse ($ciclo['pagosNoPagados'] as $pago)
        <tr>
            <td align="center">{{ $pago->Referencia ?? '-' }}</td>
            <td align="center">{{ $pago->concepto->nombreConceptoDePago ?? '-' }}</td>
            <td align="center">{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
            <td align="center">{{ $pago->costo_concepto_mostrar ?? 0}}</td>
            <td align="center">{{ $pago->descuentoDeBeca ?? 0 }}</td>
            <td align="center">{{ $pago->descuentoDePago ?? 0 }}</td>
            <td align="center">{{ $pago->recargo_concepto ?? 0 }}</td>
            <td align="center">{{ $pago->montoAPagar ?? 0 }}</td>
            <td align="center">{{ $pago->referenciaOriginal ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" align="center">No hay pagos no pagados.</td>
        </tr>
        @endforelse
    </table>

    <br>

    <!-- ================= PAGOS PENDIENTES ================= -->

    <table>

        <colgroup>
            <col style="width:300px;"> <!-- A -->
            <col style="width:300px;"> <!-- B -->
            <col style="width:180px;"> <!-- C -->
            <col style="width:200px;"> <!-- D -->
            <col style="width:180px;">  <!-- E -->
            <col style="width:180px;">  <!-- F -->
            <col style="width:180px;">  <!-- G -->
            <col style="width:180px;"> <!-- H -->
            <col style="width:300px;"> <!-- I -->
        </colgroup>

        <tr>
            <td colspan="9" align="center"><b>PAGOS PENDIENTES</b></td>
        </tr>
        <tr>
            <th><b>Referencia</b></th>
            <th><b>Concepto</b></th>
            <th><b>Fecha límite</b></th>
            <th><b>Importe total</b></th>
            <th><b>Beca (-)</b></th>
            <th><b>Descuento (-)</b></th>
            <th><b>Recargo (+)</b></th>
            <th><b>Total a pagar</b></th>
            <th><b>Referencia original</b></th>
        </tr>

        @forelse ($ciclo['pagosPendientes'] as $pago)
        <tr>
            <td align="center">{{ $pago->Referencia ?? '-' }}</td>
            <td align="center">{{ $pago->concepto->nombreConceptoDePago ?? '-' }}</td>
            <td align="center">{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
            <td align="center">{{ $pago->costo_concepto_mostrar ?? 0 }}</td>
            <td align="center">{{ $pago->descuentoDeBeca ?? 0 }}</td>
            <td align="center">{{ $pago->descuentoDePago ?? 0 }}</td>
            <td align="center">{{ $pago->recargo_concepto ?? 0 }}</td>
            <td align="center">{{ $pago->montoAPagar ?? 0}}</td>
            <td align="center">{{ $pago->referenciaOriginal ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" align="center">No hay pagos pendientes.</td>
        </tr>
        @endforelse
    </table>

    <br>

    <!-- ================= PAGOS APROBADOS ================= -->

    <table>

        <colgroup>
            <col style="width:300px;"> <!-- A -->
            <col style="width:300px;"> <!-- B -->
            <col style="width:180px;"> <!-- C -->
            <col style="width:200px;"> <!-- D -->
            <col style="width:180px;">  <!-- E -->
            <col style="width:180px;">  <!-- F -->
            <col style="width:180px;">  <!-- G -->
            <col style="width:180px;"> <!-- H -->
            <col style="width:300px;"> <!-- I -->
        </colgroup>

        <tr>
            <td colspan="9" align="center"><b>PAGOS APROBADOS</b></td>
        </tr>
        <tr>
            <th><b>Referencia</b></th>
            <th><b>Concepto</b></th>
            <th><b>Aportación</b></th>
            <th><b>Fecha de pago</b></th>
            <th><b>Método de pago</b></th>
            <th><b>Abono a saldo</b></th>
            <th><b>Abono a recargos</b></th>
            <th colspan="2"><b>Total</b></th>
        </tr>

        @forelse ($ciclo['pagosAprobados'] as $pago)
        <tr>
            <td align="center">{{ $pago->Referencia ?? '-' }}</td>
            <td align="center">{{ $pago->concepto->nombreConceptoDePago ?? '-' }}</td>
            <td align="center">{{ $pago->aportacion ?? '-' }}</td>
            <td align="center">{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
            <td align="center">{{ $pago->idTipoDePago == 1 ? 'Efectivo' : ($pago->idTipoDePago == 3 ? 'Transferencia' : '-') }}</td>
            <td align="center">{{ $pago->abono_saldo ?? 0 }}</td>
            <td align="center">{{ $pago->abono_recargo ?? 0}}</td>
            <td align="center" colspan="2">
                {{ $pago->montoAPagar ?? 0 }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" align="center">No hay pagos aprobados.</td>
        </tr>
        @endforelse
    </table>

    <br>

    <!-- ================= OTROS PAGOS ================= -->

    <table>

        <colgroup>
            <col style="width:300px;"> <!-- A -->
            <col style="width:300px;"> <!-- B -->
            <col style="width:180px;"> <!-- C -->
            <col style="width:200px;"> <!-- D -->
            <col style="width:180px;">  <!-- E -->
            <col style="width:180px;">  <!-- F -->
            <col style="width:180px;">  <!-- G -->
            <col style="width:180px;"> <!-- H -->
            <col style="width:300px;"> <!-- I -->
        </colgroup>

        <tr>
            <td colspan="9" align="center"><b>OTROS PAGOS</b></td>
        </tr>
        <tr>
            <th><b>Referencia</b></th>
            <th><b>Concepto</b></th>
            <th><b>Fecha límite</b></th>
            <th><b>Fecha de pago</b></th>
            <th><b>Método de pago</b></th>
            <th colspan="2"><b>Total</b></th>
            <th colspan="2"><b>Estatus</b></th>
        </tr>

        @forelse ($ciclo['otrosPagos'] as $pago)
        <tr>
            <td align="center">{{ $pago->Referencia ?? '-' }}</td>
            <td align="center">{{ $pago->concepto->nombreConceptoDePago ?? '-' }}</td>
            <td align="center">{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
            <td align="center">{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
            <td align="center">{{ $pago->idTipoDePago == 1 ? 'Efectivo' : ($pago->idTipoDePago == 3 ? 'Transferencia' : '-') }}</td>
            <td align="center" colspan="2">{{ $pago->montoAPagar ?? 0}}</td>
            <td align="center" colspan="2">{{ $pago->estatus->nombreTipoDeEstatus ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" align="center">No hay otros pagos registrados.</td>
        </tr>
        @endforelse
    </table>

</body>
</html>