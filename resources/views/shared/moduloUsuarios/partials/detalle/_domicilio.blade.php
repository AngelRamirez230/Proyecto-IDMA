<h3 class="subtitulo-form" style="margin: 18px 0 10px 0;">Domicilio</h3>

@php
    $dom = $u->domicilio;

    $loc = $dom?->localidad;
    $mun = $loc?->municipio;
    $ent = $mun?->entidad;
    $pai = $ent?->pais;
@endphp

<table class="tabla" style="margin-bottom: 18px;">
    <tbody class="tabla-cuerpo">
        <tr class="tabla-fila">
            <td><strong>Código postal</strong></td>
            <td>{{ $dom?->codigoPostal ?: '—' }}</td>
            <td><strong>Colonia</strong></td>
            <td>{{ $dom?->colonia ?: '—' }}</td>
        </tr>

        <tr class="tabla-fila">
            <td><strong>Calle</strong></td>
            <td>{{ $dom?->calle ?: '—' }}</td>
            <td><strong>Número exterior</strong></td>
            <td>{{ $dom?->numeroExterior ?: '—' }}</td>
        </tr>

        <tr class="tabla-fila">
            <td><strong>Número interior</strong></td>
            <td>{{ $dom?->numeroInterior ?: '—' }}</td>
            <td><strong>Localidad</strong></td>
            <td>{{ $loc?->nombreLocalidad ?: '—' }}</td>
        </tr>

        <tr class="tabla-fila">
            <td><strong>Municipio</strong></td>
            <td>{{ $mun?->nombreMunicipio ?: '—' }}</td>
            <td><strong>Entidad</strong></td>
            <td>{{ $ent?->nombreEntidad ?: '—' }}</td>
        </tr>

        <tr class="tabla-fila">
            <td><strong>País</strong></td>
            <td>{{ $pai?->nombrePais ?: '—' }}</td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>