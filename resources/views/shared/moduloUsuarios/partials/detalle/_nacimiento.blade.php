<h3 class="subtitulo-form" style="margin: 18px 0 10px 0;">Lugar de nacimiento</h3>

@php
    $locN = $u->localidadNacimiento;
    $munN = $locN?->municipio;
    $entN = $munN?->entidad;
    $paiN = $entN?->pais;
@endphp

<table class="tabla" style="margin-bottom: 18px;">
    <tbody class="tabla-cuerpo">
        <tr class="tabla-fila">
            <td><strong>País de nacimiento</strong></td>
            <td>{{ $paiN?->nombrePais ?: '—' }}</td>
            <td><strong>Entidad de nacimiento</strong></td>
            <td>{{ $entN?->nombreEntidad ?: '—' }}</td>
        </tr>

        <tr class="tabla-fila">
            <td><strong>Municipio de nacimiento</strong></td>
            <td>{{ $munN?->nombreMunicipio ?: '—' }}</td>
            <td><strong>Localidad de nacimiento</strong></td>
            <td>{{ $locN?->nombreLocalidad ?: '—' }}</td>
        </tr>
    </tbody>
</table>