<h3 class="subtitulo-form" style="margin: 18px 0 10px 0;">Datos personales</h3>

<table class="tabla" style="margin-bottom: 18px;">
    <tbody class="tabla-cuerpo">
        <tr class="tabla-fila">
            <td><strong>Tipo de usuario</strong></td>
            <td>{{ $u->tipoDeUsuario->nombreTipoDeUsuario ?? 'N/D' }}</td>
            <td><strong>Estatus</strong></td>
            <td>{{ $u->estatus->nombreTipoDeEstatus ?? 'N/D' }}</td>
        </tr>

        <tr class="tabla-fila">
            <td><strong>Primer nombre</strong></td>
            <td>{{ $u->primerNombre ?? 'N/D' }}</td>
            <td><strong>Segundo nombre</strong></td>
            <td>{{ $u->segundoNombre ?: '—' }}</td>
        </tr>

        <tr class="tabla-fila">
            <td><strong>Primer apellido</strong></td>
            <td>{{ $u->primerApellido ?? 'N/D' }}</td>
            <td><strong>Segundo apellido</strong></td>
            <td>{{ $u->segundoApellido ?: '—' }}</td>
        </tr>

        <tr class="tabla-fila">
            <td><strong>Sexo</strong></td>
            <td>{{ $u->sexo->nombreSexo ?? 'N/D' }}</td>
            <td><strong>Estado civil</strong></td>
            <td>{{ $u->estadoCivil->nombreEstadoCivil ?? 'N/D' }}</td>
        </tr>

        <tr class="tabla-fila">
            <td><strong>Teléfono</strong></td>
            <td>{{ $u->telefono ?: '—' }}</td>
            <td><strong>Teléfono fijo</strong></td>
            <td>{{ $u->telefonoFijo ?: '—' }}</td>
        </tr>

        <tr class="tabla-fila">
            <td><strong>Fecha de nacimiento</strong></td>
            <td>{{ $u->fechaDeNacimiento ? \Carbon\Carbon::parse($u->fechaDeNacimiento)->format('d/m/Y') : '—' }}</td>
            <td><strong>Correo institucional</strong></td>
            <td>{{ $u->correoInstitucional ?: '—' }}</td>
        </tr>

        <tr class="tabla-fila">
            <td><strong>Correo electrónico</strong></td>
            <td>{{ $u->correoElectronico ?: '—' }}</td>
            <td><strong>Nombre de usuario</strong></td>
            <td>{{ $u->nombreUsuario ?: '—' }}</td>
        </tr>

        <tr class="tabla-fila">
            <td><strong>RFC</strong></td>
            <td>{{ $u->RFC ?: '—' }}</td>
            <td><strong>CURP</strong></td>
            <td>{{ $u->CURP ?: '—' }}</td>
        </tr>
    </tbody>
</table>
