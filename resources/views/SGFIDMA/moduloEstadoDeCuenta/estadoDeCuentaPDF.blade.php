<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>
		Estado de Cuenta - {{ $ciclo['nombreCiclo'] ?? '' }}
	</title>

	<style>
		@page {
			margin: 10px;
		}

		body {
			font-family: DejaVu Sans, sans-serif;
			font-size: 7px;
			margin: 0;
		}

		h1 {
			text-align: center;
			color: #79272C;
			margin-bottom: 4px;
			font-size: 12px;
		}

		h2 {
			text-align: center;
			margin-top: 10px;
			margin-bottom: 6px;
			color: #79272C;
			font-size: 9px;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 10px;
			table-layout: fixed;
		}

		th,
		td {
			border: 1px solid #5B5B5B;
			padding: 3px;
			text-align: center;
			word-wrap: break-word;
		}

		th {
			font-weight: bold;
		}

		.contenedor-superior td {
			border: none !important;
		}

		.encabezado-principal {
            background-color: #79272C;
            color: white;
            font-weight: bold;
        }

        .encabezado-columnas {
            background-color: #79272C !important;  /* ← mismo vino */
            color: white;
            font-weight: bold;
        }

		tr:nth-child(even) {
			background-color: #f2f2f2;
		}

		.resumen td:first-child {
			text-align: left;
		}

		.resumen td:last-child {
			text-align: right;
		}

		.tablaVacia {
			text-align: center;
			font-weight: bold;
			color: #79272C;
		}
	</style>
</head>

<body>

	<h1>ESTADO DE CUENTA</h1>
	<h2>{{ $ciclo['nombreCiclo'] ?? '-' }}</h2>

	<!-- ================= CONTENEDOR DOS COLUMNAS ================= -->

	<table class="contenedor-superior" style="margin-bottom:15px;">
		<tr>

			<!-- IZQUIERDA -->
			<td width="60%" valign="top">

				<table>
					<tr>
						<th colspan="4" class="encabezado-principal">
							DATOS GENERALES DEL ESTUDIANTE
						</th>
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

			<!-- ESPACIO ENTRE TABLAS -->
			<td width="5%"></td>

			<!-- DERECHA -->
			<td width="35%" valign="top">

				<table class="resumen">
					<tr>
						<th colspan="2" class="encabezado-principal">
							RESUMEN DE LA CUENTA
						</th>
					</tr>

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
				</table>

			</td>

		</tr>
	</table>

	<h2>DETALLE DE MOVIMIENTOS</h2>

	<!-- ================= PAGOS NO PAGADOS ================= -->

	<table>
		<tr>
			<th colspan="9" class="encabezado-principal">PAGOS NO PAGADOS</th>
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
	</table>

	<!-- ================= PAGOS PENDIENTES ================= -->

	<table>
		<tr>
			<th colspan="9" class="encabezado-principal">PAGOS PENDIENTES</th>
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
	</table>

	<!-- ================= PAGOS APROBADOS ================= -->

	<table>
		<tr>
			<th colspan="8" class="encabezado-principal">PAGOS APROBADOS</th>
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
					{{ $pago->idTipoDePago == 1 ? 'Efectivo' : ($pago->idTipoDePago == 3 ? 'Transferencia' : '-') }}
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
	</table>

	<!-- ================= OTROS PAGOS ================= -->

	<table>
		<tr>
			<th colspan="7" class="encabezado-principal">OTROS PAGOS</th>
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
					{{ $pago->idTipoDePago == 1 ? 'Efectivo' : ($pago->idTipoDePago == 3 ? 'Transferencia' : '-') }}
				</td>
				<td>${{ number_format($pago->montoAPagar, 2) }}</td>
				<td>{{ $pago->estatus->nombreTipoDeEstatus ?? '-' }}</td>
			</tr>
		@empty
			<tr>
				<td colspan="7" class="tablaVacia">No hay otros pagos.</td>
			</tr>
		@endforelse
	</table>

</body>
</html>