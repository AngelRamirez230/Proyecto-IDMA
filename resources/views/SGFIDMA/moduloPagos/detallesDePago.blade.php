<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de pago</title>
    @vite(['resources/css/app.css'])
</head>
<body>

@include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="titulo-form2">Detalles de pago</h1>

        <div class="detalle-usuario__header">
            <div class="detalle-usuario__identidad">
                <div class="detalle-usuario__nombre">
                    Número de Referencia: {{ $pago->Referencia }}
                </div>
            </div>

            <div class="detalle-usuario__acciones">
                <a href="{{ route('consultaPagos') }}"
                class="btn-boton-formulario btn-cancelar">
                    Volver
                </a>

                @if($pago->idEstatus == 3)
                    <a href="{{ route('pagos.recibo', $pago->Referencia) }}"
                    class="btn-boton-formulario2 btn-accion">
                        Descargar recibo
                    </a>
                @endif
            </div>
        </div>

        <section class="consulta-tabla-contenedor detalle-usuario">

            <!-- =============================
                TABLA
            ============================== -->
            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Dato</th>
                        <th>Descripción</th>
                    </tr>
                </thead>

                <tbody class="tabla-cuerpo">

                    <!-- ESTUDIANTE -->
                    <tr>
                        <td>Estudiante</td>
                        <td>
                            {{ $pago->estudiante->usuario->primerNombre }}
                            {{ $pago->estudiante->usuario->segundoNombre }}
                            {{ $pago->estudiante->usuario->primerApellido }}
                            {{ $pago->estudiante->usuario->segundoApellido }}
                        </td>
                    </tr>

                    <tr>
                        <td>Matrícula</td>
                        <td>{{ $pago->estudiante->matriculaAlfanumerica }}</td>
                    </tr>

                    <!-- CONCEPTO -->
                    <tr>
                        <td>Concepto de pago</td>
                        <td>{{ $pago->concepto->nombreConceptoDePago ?? 'Sin concepto' }}</td>
                    </tr>

                    <tr>
                        <td>Costo</td>
                        <td>
                            ${{ $pago->montoAPagar ?? 'Sin costo'}}
                        </td>
                    </tr>

                    <!-- FECHAS -->
                    <tr>
                        <td>Fecha de generación</td>
                        <td>
                            {{ $pago->fechaGeneracionDePago->format('d/m/Y') }}
                        </td>
                    </tr>

                    <tr>
                        <td>Fecha límite de pago</td>
                        <td>
                            {{ $pago->fechaLimiteDePago->format('d/m/Y') }}
                        </td>
                    </tr>

                    <tr>
                        <td>Fecha de pago</td>
                        <td>
                            @if($pago->fechaDePago)
                                {{ \Carbon\Carbon::parse($pago->fechaDePago)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>

                    <!-- ESTATUS -->
                    <tr>
                        <td>Estatus</td>
                        <td>
                            {{ $pago->estatus->nombreTipoDeEstatus ?? 'Sin estatus' }}
                        </td>
                    </tr>

                    <!-- APORTACIÓN -->
                    <tr>
                        <td>Aportación registrada</td>
                        <td>
                            @if($pago->aportacion)
                                {{ $pago->aportacion }}
                            @else
                                {{$pago->concepto->nombreConceptoDePago}}
                            @endif
                        </td>
                    </tr>

                    @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11))
                        <tr class="tabla-subtitulo">
                            <td colspan="2"><strong>Información administrativa</strong></td>
                        </tr>

                        <tr>
                            <td>Tipo de registro</td>
                            <td>{{ $pago->tipoDeRegistro ?? '-' }}</td>
                        </tr>

                        <tr>
                            <td>Número de operación (BAZ)</td>
                            <td>{{ $pago->numeroDeOperaciónBAZ ?? '-' }}</td>
                        </tr>

                        <tr>
                            <td>Sucursal</td>
                            <td>{{ $pago->numeroDeSucursal ?? '-' }}</td>
                        </tr>

                        <tr>
                            <td>Forma de pago</td>
                            <td>{{ $pago->idTipoDePago ?? '-' }}</td>
                        </tr>

                        <tr>
                            <td>Importe del pago</td>
                            <td>
                                {{ $pago->ImporteDePago
                                    ? '$' . number_format($pago->ImporteDePago, 2)
                                    : '-' }}
                            </td>
                        </tr>

                        <tr>
                            <td>Comisión</td>
                            <td>
                                {{ $pago->comisión
                                    ? '$' . number_format($pago->comisión, 2)
                                    : '-' }}
                            </td>
                        </tr>

                        <tr>
                            <td>IVA</td>
                            <td>
                                {{ $pago->IVA
                                    ? '$' . number_format($pago->IVA, 2)
                                    : '-' }}
                            </td>
                        </tr>

                        <tr>
                            <td>Importe neto</td>
                            <td>
                                {{ $pago->ImporteNeto
                                    ? '$' . number_format($pago->ImporteNeto, 2)
                                    : '-' }}
                            </td>
                        </tr>

                    @endif


                </tbody>
            </table>
        </section>
    </main>

</body>
</html>
