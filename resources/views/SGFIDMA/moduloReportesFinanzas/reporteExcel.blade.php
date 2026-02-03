<table>
    <colgroup>
        <col style="width:300px;">
        <col style="width:300px;">
        <col style="width:400px;">
        <col style="width:120px;">
        <col style="width:120px;">
        <col style="width:120px;">
        <col style="width:120px;">
        <col style="width:120px;">
    </colgroup>

    {{-- TÍTULO --}}
    <tr>
        <td colspan="8" style="text-align:center; font-weight:bold; font-size:16px;">
            REPORTE DE PAGOS {{ strtoupper($tipo) }}
        </td>
    </tr>

    {{-- RANGO --}}
    <tr>
        <td colspan="8" style="text-align:center;">
            Del {{ $inicio->format('d/m/Y') }} al {{ $fin->format('d/m/Y') }}
        </td>
    </tr>

    <tr><td colspan="8"></td></tr>

    {{-- ENCABEZADOS --}}
    <thead>
        <tr>
            <th style="text-align:center; font-weight:bold;">Estudiante</th>
            <th style="text-align:center; font-weight:bold;">Referencia</th>
            <th style="text-align:center; font-weight:bold;">Concepto</th>
            <th style="text-align:center; font-weight:bold;">Monto</th>
            <th style="text-align:center; font-weight:bold;">Fecha generación</th>
            <th style="text-align:center; font-weight:bold;">Fecha límite</th>
            <th style="text-align:center; font-weight:bold;">Fecha pago</th>
            <th style="text-align:center; font-weight:bold;">Estatus</th>
        </tr>
    </thead>

    {{-- DATOS --}}
    <tbody>
        @foreach ($pagos as $pago)
        <tr>
            <td style="text-align:center;">
                {{ $pago->estudiante->usuario->primerNombre }}
                {{ $pago->estudiante->usuario->segundoNombre }}
                {{ $pago->estudiante->usuario->primerApellido }}
                {{ $pago->estudiante->usuario->segundoApellido }}
            </td>
            <td style="text-align:center;">{{ $pago->Referencia }}</td>
            <td style="text-align:center;">{{ $pago->concepto->nombreConceptoDePago }}</td>
            <td style="text-align:center;">{{ number_format($pago->montoAPagar, 2, '.', '') }}</td>
            <td style="text-align:center;">{{ $pago->fechaGeneracionDePago?->format('d/m/Y') ?? '-' }}</td>
            <td style="text-align:center;">{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
            <td style="text-align:center;">{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
            <td style="text-align:center;">{{ $pago->estatus->nombreTipoDeEstatus }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
