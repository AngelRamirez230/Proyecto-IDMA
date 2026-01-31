<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 landscape;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            color: #79272C;
        }
        
        .rango-fechas {
            text-align: center;
            font-size: 11px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }

        thead {
            display: table-row-group; /* ← clave */
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            word-wrap: break-word;
        }


        th {
            background-color: #79272C;
            color: #fff;
        }

        .rango-fechas {
            text-align: center;
            font-size: 11px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

@php
    $tipos = [
        'aprobados'  => 'aprobados',
        'pendientes' => 'pendientes',
        'rechazados' => 'rechazados',
        'kardex'     => 'kárdex',
    ];
@endphp



<h1> Reporte de pagos {{ strtolower($tipo) ?? '' }}</h1>
<p class="rango-fechas">
    Del {{ $inicio->format('d/m/Y') }} al {{ $fin->format('d/m/Y') }}
</p>

<table>
    <colgroup>
        <col style="width: 150px;">
        <col style="width: 75px;">
        <col style="width: 120px;">
        <col style="width: 80px;">
        <col style="width: 80px;">
        <col style="width: 75px;">
        <col style="width: 70px;">
    </colgroup>

    <thead>
        <tr>
            <th>Estudiante</th>
            <th>Referencia</th>
            <th>Concepto</th>
            <th>Monto</th>
            <th>Fecha generación</th>
            <th>Fecha límite</th>
            <th>Fecha pago</th>
            <th>Estatus</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($pagos as $pago)
        <tr>
            <td>
                {{ $pago->estudiante->usuario->primerNombre }}
                {{ $pago->estudiante->usuario->segundoNombre }}
                {{ $pago->estudiante->usuario->primerApellido }}
                {{ $pago->estudiante->usuario->segundoApellido }}
            </td>
            <td>{{ $pago->Referencia }}</td>
            <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
            <td>${{ number_format($pago->montoAPagar, 2) }}</td>
            <td>{{ $pago->fechaGeneracionDePago?->format('d/m/Y') ?? '-' }}</td>
            <td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
            <td>{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
            <td>{{ $pago->estatus->nombreTipoDeEstatus }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


</body>
</html>
