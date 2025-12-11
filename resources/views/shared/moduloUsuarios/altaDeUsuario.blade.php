<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de usuario</title>
    @vite(['resources/css/app.css'])
</head>

<body>
@include('layouts.barraNavegacion')

<main class="form-container">

    {{-- TÍTULO DEL FORMULARIO --}}
    <h1 class="titulo-form">
        Alta de usuario - Rol:
        <strong>
            @if(isset($rol) && $rol == 1)
                Administrador
            @elseif(isset($rol) && $rol == 2)
                Empleado
            @elseif(isset($rol) && $rol == 3)
                Docente
            @elseif(isset($rol) && $rol == 4)
                Estudiante
            @else
                Sin rol definido
            @endif
        </strong>
    </h1>

    {{-- BLOQUE DE ERRORES DE VALIDACIÓN --}}
    @if ($errors->any())
        <div style="background:#ffdddd; padding:12px; border:1px solid #cc0000; margin:10px 0;">
            <strong>Corrige los siguientes errores:</strong>
            <ul style="margin: 8px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORMULARIO --}}
    <form action="{{ route('usuarios.store') }}" method="POST" class="formulario">
        @csrf

        {{-- ENVIAR ROL SELECCIONADO --}}
        <input type="hidden" name="rol" value="{{ $rol ?? '' }}">

        {{-- PRIMER NOMBRE --}}
        <div class="form-group">
            <label for="primer_nombre">Primer nombre:</label>
            <input
                type="text"
                id="primer_nombre"
                name="primer_nombre"
                class="input-mediano"
                placeholder="Ingresa el primer nombre"
                value="{{ old('primer_nombre') }}"
                required
            >
        </div>

        {{-- SEGUNDO NOMBRE --}}
        <div class="form-group">
            <label for="segundo_nombre">Segundo nombre:</label>
            <input
                type="text"
                id="segundo_nombre"
                name="segundo_nombre"
                class="input-mediano"
                placeholder="Ingresa el segundo nombre"
                value="{{ old('segundo_nombre') }}"
            >
        </div>

        {{-- PRIMER APELLIDO --}}
        <div class="form-group">
            <label for="primer_apellido">Primer apellido:</label>
            <input
                type="text"
                id="primer_apellido"
                name="primer_apellido"
                class="input-mediano"
                placeholder="Ingresa el primer apellido"
                value="{{ old('primer_apellido') }}"
                required
            >
        </div>

        {{-- SEGUNDO APELLIDO --}}
        <div class="form-group">
            <label for="segundo_apellido">Segundo apellido:</label>
            <input
                type="text"
                id="segundo_apellido"
                name="segundo_apellido"
                class="input-mediano"
                placeholder="Ingresa el segundo apellido"
                value="{{ old('segundo_apellido') }}"
            >
        </div>

        {{-- SEXO --}}
        <div class="form-group">
            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" class="select" required>
                <option value="" disabled {{ old('sexo') ? '' : 'selected' }}>Seleccionar</option>
                @foreach($sexos as $sx)
                    <option
                        value="{{ $sx->idSexo }}"
                        {{ old('sexo') == $sx->idSexo ? 'selected' : '' }}
                    >
                        {{ $sx->nombreSexo }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- TELÉFONO --}}
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input
                type="text"
                id="telefono"
                name="telefono"
                class="input-chico"
                placeholder="Ingresa número de teléfono"
                value="{{ old('telefono') }}"
            >
        </div>

        {{-- CORREO INSTITUCIONAL --}}
        <div class="form-group">
            <label for="emailInstitucional">Correo institucional:</label>
            <input
                type="email"
                id="emailInstitucional"
                name="emailInstitucional"
                class="input-mediano"
                placeholder="ejemplo@idma.edu.mx"
                value="{{ old('emailInstitucional') }}"
            >
        </div>

        {{-- CONTRASEÑA --}}
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input
                type="password"
                id="password"
                name="password"
                class="input-chico"
                placeholder="Escribe una contraseña"
                required
            >
        </div>

        {{-- NOMBRE DE USUARIO --}}
        <div class="form-group">
            <label for="nombreUsuario">Nombre de usuario:</label>
            <input
                type="text"
                id="nombreUsuario"
                name="nombreUsuario"
                class="input-mediano"
                placeholder="Ingresa el nombre de usuario"
                value="{{ old('nombreUsuario') }}"
                required
            >
        </div>

        {{-- FECHA NACIMIENTO --}}
        <div class="form-group">
            <label for="fechaNacimiento">Fecha de nacimiento:</label>
            <input
                type="date"
                id="fechaNacimiento"
                name="fechaNacimiento"
                max="{{ date('Y-m-d') }}"
                class="input-date"
                value="{{ old('fechaNacimiento') }}"
            >
        </div>

        {{-- CURP --}}
        <div class="form-group">
            <label for="curp">CURP:</label>
            <input
                type="text"
                id="curp"
                name="curp"
                class="input-chico"
                placeholder="CURP"
                value="{{ old('curp') }}"
            >
        </div>

        {{-- RFC --}}
        <div class="form-group">
            <label for="rfc">RFC:</label>
            <input
                type="text"
                id="rfc"
                name="rfc"
                class="input-chico"
                placeholder="RFC"
                value="{{ old('rfc') }}"
            >
        </div>

        {{-- CORREO PERSONAL --}}
        <div class="form-group">
            <label for="email">Correo electrónico:</label>
            <input
                type="email"
                id="email"
                name="email"
                class="input-mediano"
                placeholder="ejemplo@correo.com"
                value="{{ old('email') }}"
            >
        </div>

        {{-- ENTIDAD --}}
        <div class="form-group">
            <label for="entidad">Entidad:</label>
            <select id="entidad" name="entidad" class="select" required>
                <option value="" disabled {{ old('entidad') ? '' : 'selected' }}>Seleccionar</option>
                @foreach($entidades as $e)
                    <option
                        value="{{ $e->idEntidad }}"
                        {{ old('entidad') == $e->idEntidad ? 'selected' : '' }}
                    >
                        {{ $e->nombreEntidad }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- MUNICIPIO --}}
        <div class="form-group">
            <label for="municipio">Municipio:</label>
            <select id="municipio" name="municipio" class="select" required>
                <option value="" disabled selected>Seleccionar</option>
            </select>
        </div>

        {{-- LOCALIDAD --}}
        <div class="form-group">
            <label for="localidad">Localidad:</label>
            <select id="localidad" name="localidad" class="select" required>
                <option value="" disabled selected>Seleccionar</option>
            </select>
        </div>

        {{-- CÓDIGO POSTAL --}}
        <div class="form-group">
            <label for="codigoPostal">Código postal:</label>
            <input
                type="text"
                id="codigoPostal"
                name="codigoPostal"
                class="input-chico"
                placeholder="Código postal"
                value="{{ old('codigoPostal') }}"
            >
        </div>

        {{-- CALLE --}}
        <div class="form-group">
            <label for="calle">Calle:</label>
            <input
                type="text"
                id="calle"
                name="calle"
                class="input-grande"
                placeholder="Ingresa la calle"
                value="{{ old('calle') }}"
            >
        </div>

        {{-- NÚMERO EXTERIOR --}}
        <div class="form-group">
            <label for="numeroExterior">Número exterior:</label>
            <input
                type="text"
                id="numeroExterior"
                name="numeroExterior"
                class="input-chico"
                placeholder="Número exterior"
                value="{{ old('numeroExterior') }}"
            >
        </div>

        {{-- NÚMERO INTERIOR --}}
        <div class="form-group">
            <label for="numeroInterior">Número interior:</label>
            <input
                type="text"
                id="numeroInterior"
                name="numeroInterior"
                class="input-chico"
                placeholder="Número interior"
                value="{{ old('numeroInterior') }}"
            >
        </div>

        {{-- COLONIA --}}
        <div class="form-group">
            <label for="colonia">Colonia:</label>
            <input
                type="text"
                id="colonia"
                name="colonia"
                class="input-mediano"
                placeholder="Colonia"
                value="{{ old('colonia') }}"
            >
        </div>

        {{-- BOTONES --}}
        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Guardar</button>
            <a href="{{ route('apartadoUsuarios') }}" class="btn-boton-formulario btn-cancelar">
                Cancelar
            </a>
        </div>

    </form>
</main>

{{-- ============================================
     JS DINÁMICO PARA MUNICIPIOS Y LOCALIDADES
   ============================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const entidadSelect   = document.getElementById('entidad');
    const municipioSelect = document.getElementById('municipio');
    const localidadSelect = document.getElementById('localidad');

    entidadSelect.addEventListener('change', function () {
        const idEntidad = this.value;

        municipioSelect.innerHTML = '<option value="" disabled selected>Cargando...</option>';
        localidadSelect.innerHTML = '<option value="" disabled selected>Selecciona un municipio</option>';

        fetch(`/api/municipios/${idEntidad}`)
            .then(response => response.json())
            .then(data => {
                municipioSelect.innerHTML = '<option value="" disabled selected>Seleccionar</option>';
                data.forEach(mun => {
                    municipioSelect.innerHTML += `<option value="${mun.idMunicipio}">${mun.nombreMunicipio}</option>`;
                });
            });
    });

    municipioSelect.addEventListener('change', function () {
        const idMunicipio = this.value;

        localidadSelect.innerHTML = '<option value="" disabled selected>Cargando...</option>';

        fetch(`/api/localidades/${idMunicipio}`)
            .then(response => response.json())
            .then(data => {
                localidadSelect.innerHTML = '<option value="" disabled selected>Seleccionar</option>';
                data.forEach(loc => {
                    localidadSelect.innerHTML += `<option value="${loc.idLocalidad}">${loc.nombreLocalidad}</option>`;
                });
            });
    });

});
</script>

</body>
</html>