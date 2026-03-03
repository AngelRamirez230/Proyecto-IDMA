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

			@if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11,12))
				<a href="{{ route('estadosCuenta.seleccionarEstudiante') }}"
				   class="btn-boton-formulario2 btn-cancelar2">
					Cancelar
				</a>
			@endif

			@estudiante
				<a href="{{ route('apartadoEstadoDeCuenta') }}"
				   class="btn-boton-formulario2 btn-cancelar2">
					Cancelar
				</a>
			@endestudiante

		</div>

		@foreach ($estadoCuentaPorCiclo as $idCiclo => $ciclo)

		<div class="bloque-ciclo">

			<button class="btn-ciclo"
				onclick="document.getElementById('ciclo-{{ $idCiclo }}').classList.toggle('oculto')">
				CICLO: {{ strtoupper($ciclo['nombreCiclo']) }}
			</button>

			<div id="ciclo-{{ $idCiclo }}" class="contenido-ciclo oculto">

				<!-- BOTONES EXPORTACIÓN -->
				<div class="form-group2 acciones-reporte">

					<form action="{{ route('estadoCuenta.pdf', [$estudiante->idEstudiante, $idCiclo]) }}" method="POST">
						@csrf
						<button class="btn-boton-formulario2">Exportar PDF</button>
					</form>

					@if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11,12))
						<form action="{{ route('estadoCuenta.excel', [$estudiante->idEstudiante, $idCiclo]) }}" method="POST">
							@csrf
							<button class="btn-boton-formulario2">Exportar Excel</button>
						</form>
					@endif

				</div>

				<!-- DATOS GENERALES -->
				<section class="consulta-tabla-contenedor">

					<div class="estado-cuenta-tablas-compacto">

						<table class="tabla-compacta tabla-datos tabla-compacta-secundaria">
							<thead>
								<tr>
									<th colspan="9">DATOS GENERALES DEL ESTUDIANTE</th>
								</tr>
							</thead>
							<tbody>

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

						<table class="tabla-compacta tabla-resumen tabla-compacta-secundaria2 resumen-cuenta">
							<thead>
								<tr>
									<th colspan="2">RESUMEN DE LA CUENTA</th>
								</tr>
							</thead>
							<tbody>
								<tr><td><strong>Importe total</strong></td><td class="monto">${{ number_format($ciclo['importeTotal'], 2) }}</td></tr>
								<tr><td><strong>Becas (-)</strong></td><td class="monto">${{ number_format($ciclo['becasTotal'], 2) }}</td></tr>
								<tr><td><strong>Descuentos</strong></td><td class="monto">${{ number_format($ciclo['descuentosTotal'] ?? 0, 2) }}</td></tr>
								<tr><td><strong>Saldo a pagar</strong></td><td class="monto">${{ number_format($ciclo['saldoAPagar'] ?? 0, 2) }}</td></tr>
								<tr><td><strong>Abonos a saldo (-)</strong></td><td class="monto">${{ number_format($ciclo['abonosASaldo'] ?? 0, 2) }}</td></tr>
								<tr><td><strong>Abono a recargos</strong></td><td class="monto">${{ number_format($ciclo['abonoARecargos'] ?? 0, 2) }}</td></tr>
								<tr><td><strong>Saldo pendiente</strong></td><td class="monto">${{ number_format($ciclo['saldoPendiente'] ?? 0, 2) }}</td></tr>
								<tr><td><strong>Saldo vencido</strong></td><td class="monto">${{ number_format($ciclo['saldoVencido'] ?? 0, 2) }}</td></tr>
								<tr><td><strong>Recargos (+)</strong></td><td class="monto">${{ number_format($ciclo['recargosTotal'] ?? 0, 2) }}</td></tr>
								<tr><td><strong>Saldo actual</strong></td><td class="monto">${{ number_format($ciclo['saldoActual'] ?? 0, 2) }}</td></tr>
							</tbody>
						</table>

					</div>

				</section>

				<h1 class="titulo-movimientos">DETALLES DE MOVIMIENTOS</h1>

				<!-- NO PAGADOS -->
				<section class="consulta-tabla-contenedor">
					<table class="tabla-compacta">
						<thead>
							<tr><th colspan="9">NO PAGADOS</th></tr>
							<tr>
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
						<tbody>
							@forelse ($ciclo['pagosNoPagados'] as $pago)
								<tr>
									<td>{{ $pago->Referencia }}</td>
									<td>{{ $pago->concepto->nombreConceptoDePago }}</td>
									<td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
									<td class="monto">${{ number_format($pago->costo_concepto_mostrar, 2) }}</td>
									<td class="monto">${{ number_format($pago->descuentoDeBeca, 2) }}</td>
									<td class="monto">${{ number_format($pago->descuentoDePago, 2) }}</td>
									<td class="monto">${{ number_format($pago->recargo_concepto, 2) }}</td>
									<td class="monto">${{ number_format($pago->montoAPagar, 2) }}</td>
									<td>{{ $pago->referenciaOriginal ?? '-' }}</td>
								</tr>
							@empty
								<tr>
									<td colspan="9" class="estado-cuenta-tabla-vacia">No hay pagos no pagados.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</section>

				<!-- PAGOS PENDIENTES -->
				<section class="consulta-tabla-contenedor">
					<table class="tabla-compacta">
						<thead>
							<tr><th colspan="9">PAGOS PENDIENTES</th></tr>
							<tr>
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
						<tbody>
							@forelse ($ciclo['pagosPendientes'] as $pago)
								<tr>
									<td>{{ $pago->Referencia }}</td>
									<td>{{ $pago->concepto->nombreConceptoDePago }}</td>
									<td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
									<td class="monto">${{ number_format($pago->costo_concepto_mostrar, 2) }}</td>
									<td class="monto">${{ number_format($pago->descuentoDeBeca, 2) }}</td>
									<td class="monto">${{ number_format($pago->descuentoDePago, 2) }}</td>
									<td class="monto">${{ number_format($pago->recargo_concepto, 2) }}</td>
									<td class="monto">${{ number_format($pago->montoAPagar, 2) }}</td>
									<td>{{ $pago->referenciaOriginal ?? '-' }}</td>
								</tr>
							@empty
								<tr>
									<td colspan="9" class="estado-cuenta-tabla-vacia">No hay pagos pendientes.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</section>

				<!-- PAGOS APROBADOS -->
				<section class="consulta-tabla-contenedor">
					<table class="tabla-compacta">
						<thead>
							<tr><th colspan="8">PAGOS APROBADOS</th></tr>
							<tr>
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
						<tbody>
							@forelse ($ciclo['pagosAprobados'] as $pago)
								<tr>
									<td>{{ $pago->Referencia }}</td>
									<td>{{ $pago->concepto->nombreConceptoDePago }}</td>
									<td>{{ $pago->aportacion ?? '-' }}</td>
									<td>{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
									<td>
										{{ $pago->idTipoDePago == 1 ? 'Efectivo' :
										   ($pago->idTipoDePago == 3 ? 'Transferencia' : '-') }}
									</td>
									<td class="monto">${{ number_format($pago->abono_saldo, 2) }}</td>
									<td class="monto">${{ number_format($pago->abono_recargo, 2) }}</td>
									<td class="monto">${{ number_format($pago->montoAPagar, 2) }}</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="estado-cuenta-tabla-vacia">No hay pagos aprobados.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</section>

				<!-- OTROS PAGOS -->
				<section class="consulta-tabla-contenedor">
					<table class="tabla-compacta">
						<thead>
							<tr><th colspan="7">OTROS PAGOS</th></tr>
							<tr>
								<th>Referencia</th>
								<th>Concepto</th>
								<th>Fecha límite</th>
								<th>Fecha de pago</th>
								<th>Método de pago</th>
								<th>Total</th>
								<th>Estatus</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($ciclo['otrosPagos'] as $pago)
								<tr>
									<td>{{ $pago->Referencia }}</td>
									<td>{{ $pago->concepto->nombreConceptoDePago }}</td>
									<td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
									<td>{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
									<td>
										{{ $pago->idTipoDePago == 1 ? 'Efectivo' :
										   ($pago->idTipoDePago == 3 ? 'Transferencia' : '-') }}
									</td>
									<td class="monto">${{ number_format($pago->montoAPagar, 2) }}</td>
									<td>{{ $pago->estatus->nombreTipoDeEstatus ?? '-' }}</td>
								</tr>
							@empty
								<tr>
									<td colspan="7" class="estado-cuenta-tabla-vacia">No hay otros pagos registrados.</td>
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