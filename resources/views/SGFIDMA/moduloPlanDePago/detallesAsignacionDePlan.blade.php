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

/* ===== PAGOS CREADOS ===== */
foreach ($creados as $id => $grupo) {

    if (!isset($estudiantes[$id])) {
        $estudiantes[$id] = [
            'estudiante' => $grupo['estudiante'] ?? 'Estudiante sin nombre',
            'pagos'      => []
        ];
    }

    foreach ($grupo['pagos'] ?? [] as $pago) {
        $estudiantes[$id]['pagos'][] = [
            'referencia' => $pago['referencia'],
            'concepto'   => $pago['concepto'],
            'fecha'      => $pago['fecha'],
            'tipo'       => 'creado',
            'idConcepto' => $pago['idConcepto'], 
        ];
    }
}

/* ===== PAGOS EXISTENTES ===== */
foreach ($duplicados as $id => $grupo) {

    if (!isset($estudiantes[$id])) {
        $estudiantes[$id] = [
            'estudiante' => $grupo['estudiante'] ?? 'Estudiante sin nombre',
            'pagos'      => []
        ];
    }

    foreach ($grupo['pagos'] ?? [] as $pago) {
        $estudiantes[$id]['pagos'][] = [
            'referencia' => $pago['referencia'],
            'concepto'   => $pago['concepto'],
            'fecha'      => $pago['fecha'],
            'tipo'       => 'existente',
            'idConcepto' => $pago['idConcepto'],
        ];
    }
}
@endphp




<main class="consulta">

    <h1 class="titulo-form2">Detalles de asignación del plan de pago</h1>

    <section class="consulta-controles">
        <div class="consulta-selects">
            <a href="{{ route('admin.planPago.asignar.create') }}"
            class="btn-boton-formulario2">
                Volver
            </a>
        </div>
    </section>

    @if(!empty($noAplicados))

    <section class="consulta-tabla-contenedor">

        <h2 class="consulta-titulo titulo-chico">
            Estudiantes a los que NO se aplicó el plan
        </h2>

        <table class="tabla">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($noAplicados as $item)
                    <tr class="fila-suspendida">
                        <td>{{ $item['estudiante'] }}</td>
                        <td>{{ $item['motivo'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </section>

    @endif


    {{-- ================= TABLAS POR ESTUDIANTE ================= --}}
    @foreach($estudiantes as $grupo)

        @php
            $pagos = collect($grupo['pagos']);

            // ¿Tiene al menos un pago creado?
            $tienePagosCreados = $pagos->contains(fn($p) => $p['tipo'] === 'creado');

            if ($tienePagosCreados) {

            
                $principal = $pagos->first(fn($p) =>
                    in_array($p['idConcepto'], [1, 30])
                );

                
                $colegiaturas = $pagos
                    ->reject(fn($p) => in_array($p['idConcepto'], [1, 30]))
                    ->sortBy(fn($p) => \Carbon\Carbon::parse($p['fecha'])->timestamp);

                
                $pagosOrdenados = collect();

                if ($principal) {
                    $pagosOrdenados->push($principal);
                }

                $pagosOrdenados = $pagosOrdenados->merge($colegiaturas);
            }
        @endphp

        @if($tienePagosCreados)

            <section class="consulta-tabla-contenedor">

                <h2 class="consulta-titulo titulo-chico">
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

                        @foreach($pagosOrdenados as $pago)
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

        @endif

    @endforeach




    {{-- ================= SIN RESULTADOS ================= --}}
    @if(empty($estudiantes))
        <p class="tablaVacia">No hay información para mostrar.</p>
    @endif

</main>

</body>
</html>
