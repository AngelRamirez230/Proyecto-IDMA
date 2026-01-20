<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de asignación de plan de pagos</title>
    @vite(['resources/css/app.css'])
</head>
<body>

@include('layouts.barraNavegacion')

@php
    /*
    ==================================================
    UNIFICAR PAGOS CREADOS Y EXISTENTES POR ESTUDIANTE
    ==================================================
    */
    $estudiantes = [];

    foreach ($creados as $id => $grupo) {
        $estudiantes[$id]['estudiante'] = $grupo['estudiante'];
        foreach ($grupo['pagos'] as $pago) {
            $estudiantes[$id]['pagos'][] = array_merge($pago, [
                'tipo' => 'creado'
            ]);
        }
    }

    foreach ($duplicados as $id => $grupo) {
        $estudiantes[$id]['estudiante'] = $grupo['estudiante'];
        foreach ($grupo['pagos'] as $pago) {
            $estudiantes[$id]['pagos'][] = array_merge($pago, [
                'tipo' => 'existente'
            ]);
        }
    }
@endphp

<main class="consulta">

    <h1 class="titulo-form2">Detalle de asignación del plan de pago</h1>

    <section class="consulta-controles">
        <div class="consulta-selects">
            <a href="{{ route('admin.planPago.asignar.create') }}"
            class="btn-boton-formulario2">
                Volver
            </a>
        </div>
    </section>

    {{-- ================= TABLAS POR ESTUDIANTE ================= --}}
    @foreach($estudiantes as $grupo)

        <section class="consulta-tabla-contenedor">

            <h2 class="consulta-titulo">
                {{ $grupo['estudiante'] }} — Detalle de pagos
            </h2>

            <table class="tabla">
                <thead>
                    <tr>
                        <th>Referencia</th>
                        <th>Concepto</th>
                        <th>Fecha límite de pago</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($grupo['pagos'] as $pago)
                        <tr class="{{ $pago['tipo'] === 'existente' ? 'fila-suspendida' : '' }}">
                            <td>{{ $pago['referencia'] }}</td>
                            <td>{{ $pago['concepto'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($pago['fecha'])->format('d/m/Y') }}</td>
                            <td>
                                @if($pago['tipo'] === 'creado')
                                    <span class="estatus-activo">Creado</span>
                                @else
                                    <span class="estatus-suspendido">Existente</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </section>

    @endforeach

    {{-- ================= SIN RESULTADOS ================= --}}
    @if(empty($estudiantes))
        <p class="tablaVacia">No hay información para mostrar.</p>
    @endif

</main>

</body>
</html>
