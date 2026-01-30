<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 20px 25px;
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
    <thead>
        <tr>
            <th>Estudiante</th>
            <th>Referencia</th>
            <th>Concepto</th>
            <th>Fecha generación</th>
            <th>Fecha límite</th>
            <th>Fecha pago</th>
            <th>Estatus</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pagos as $pago)
            <tr>
                <td style="width: 25%;">
                    {{ $pago->estudiante->usuario->primerNombre }}
                    {{ $pago->estudiante->usuario->segundoNombre }}
                    {{ $pago->estudiante->usuario->primerApellido }}
                    {{ $pago->estudiante->usuario->segundoApellido }}
                </td>
                <td style="width: 12%;">{{ $pago->Referencia }}</td>
                <td style="width: 20%;">{{ $pago->concepto->nombreConceptoDePago }}</td>
                <td style="width: 11%;">{{ optional($pago->fechaGeneracionDePago)->format('d/m/Y') }}</td>
                <td style="width: 11%;">{{ optional($pago->fechaLimiteDePago)->format('d/m/Y') }}</td>
                <td style="width: 11%;">{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
                <td style="width: 10%;">{{ $pago->estatus->nombreTipoDeEstatus }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
