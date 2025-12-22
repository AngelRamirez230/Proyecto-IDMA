<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de estudiante</title>
    @vite(['resources/css/app.css'])
</head>

<body>
@include('layouts.barraNavegacion')

<main class="form-container">

    {{-- ERRORES --}}
    @if ($errors->any())
        <div style="background:#ffdddd; padding:12px; border:1px solid #cc0000; margin:10px 0;">
            <strong>Corrige los siguientes errores:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORMULARIO --}}
    <form action="#" method="POST" class="formulario">
        @csrf

        <h1 class="titulo-form">
            Alta de estudiante
        </h1>

        {{-- ================= DATOS PERSONALES ================= --}}
        <h3 class="subtitulo-form">Datos personales</h3>

        <div class="form-group">
            <label>Primer nombre:</label>
            <input type="text" name="primer_nombre" class="input-mediano"
                   value="{{ old('primer_nombre') }}" required>
        </div>

        <div class="form-group">
            <label>Segundo nombre:</label>
            <input type="text" name="segundo_nombre" class="input-mediano"
                   value="{{ old('segundo_nombre') }}">
        </div>

        <div class="form-group">
            <label>Apellido paterno:</label>
            <input type="text" name="apellido_paterno" class="input-mediano"
                   value="{{ old('apellido_paterno') }}" required>
        </div>

        <div class="form-group">
            <label>Apellido materno:</label>
            <input type="text" name="apellido_materno" class="input-mediano"
                   value="{{ old('apellido_materno') }}" required>
        </div>

        <div class="form-group">
            <label>Sexo:</label>
            <select name="genero" class="select" required>
                <option value="">Seleccionar</option>
                @foreach($sexos as $sx)
                    <option value="{{ $sx->idSexo }}"
                        {{ old('genero') == $sx->idSexo ? 'selected' : '' }}>
                        {{ $sx->nombreSexo }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Estado civil:</label>
            <select name="estadoCivil" class="select" required>
                <option value="">Seleccionar</option>
                @foreach($estadosCiviles as $ec)
                    <option value="{{ $ec->idEstadoCivil }}"
                        {{ old('estadoCivil') == $ec->idEstadoCivil ? 'selected' : '' }}>
                        {{ $ec->nombreEstadoCivil }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Fecha de nacimiento:</label>
            <input type="date" name="fechaNacimiento" class="input-date"
                   max="{{ date('Y-m-d') }}" value="{{ old('fechaNacimiento') }}">
        </div>

        <div class="form-group">
            <label>CURP:</label>
            <input type="text" name="curp" class="input-chico"
                   value="{{ old('curp') }}">
        </div>

        <div class="form-group">
            <label>RFC:</label>
            <input type="text" name="rfc" class="input-chico"
                   value="{{ old('rfc') }}">
        </div>

        {{-- ================= CONTACTO ================= --}}
        <h3 class="subtitulo-form">Contacto</h3>

        <div class="form-group">
            <label>Teléfono:</label>
            <input type="text" name="telefono" class="input-chico"
                   value="{{ old('telefono') }}">
        </div>

        <div class="form-group">
            <label>Correo personal:</label>
            <input type="email" name="email" class="input-mediano"
                   value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label>Correo institucional:</label>
            <input type="email" name="emailInstitucional" class="input-mediano"
                   value="{{ old('emailInstitucional') }}">
        </div>

        {{-- ================= DOMICILIO ================= --}}
        <h3 class="subtitulo-form">Domicilio</h3>

        {{-- ENTIDAD --}}
        <div class="form-group">
            <label>Entidad:</label>
            <select id="entidad" name="entidad" class="select select-buscable">
                <option value="">Seleccionar</option>
                @foreach($entidades as $e)
                    <option value="{{ $e->idEntidad }}">{{ $e->nombreEntidad }}</option>
                @endforeach
            </select>
        </div>

        {{-- MUNICIPIO --}}
        <div class="form-group">
            <label>Municipio:</label>
            <div class="select-buscable-wrapper">
                <input class="input-mediano select-buscable-input"
                       placeholder="Seleccione entidad" readonly>
                <ul class="select-buscable-list"></ul>
                <select id="municipio" name="municipio" hidden disabled></select>
            </div>
        </div>

        {{-- LOCALIDAD --}}
        <div class="form-group">
            <label>Localidad:</label>
            <div class="select-buscable-wrapper">
                <input class="input-mediano select-buscable-input"
                       placeholder="Seleccione municipio" readonly>
                <ul class="select-buscable-list"></ul>
                <select id="localidad" name="localidad" hidden disabled></select>
            </div>
        </div>

        <div class="form-group">
            <label>Colonia:</label>
            <input type="text" name="colonia" class="input-mediano"
                   value="{{ old('colonia') }}">
        </div>

        <div class="form-group">
            <label>Código postal:</label>
            <input type="text" name="codigoPostal" class="input-chico"
                   value="{{ old('codigoPostal') }}">
        </div>

        <div class="form-group">
            <label>Calle:</label>
            <input type="text" name="calle" class="input-grande"
                   value="{{ old('calle') }}">
        </div>

        <div class="form-group">
            <label>Número exterior:</label>
            <input type="text" name="numeroExterior" class="input-chico"
                   value="{{ old('numeroExterior') }}">
        </div>

        <div class="form-group">
            <label>Número interior:</label>
            <input type="text" name="numeroInterior" class="input-chico"
                   value="{{ old('numeroInterior') }}">
        </div>

        {{-- ================= DATOS ACADÉMICOS ================= --}}
        <h3 class="subtitulo-form">Datos académicos</h3>

        <div class="form-group">
            <label>Matrícula numérica:</label>
            <input type="text" name="matriculaNumerica" class="input-chico"
                   value="{{ old('matriculaNumerica') }}">
        </div>

        <div class="form-group">
            <label>Matrícula alfanumérica:</label>
            <input type="text" name="matriculaAlfanumerica" class="input-mediano"
                   value="{{ old('matriculaAlfanumerica') }}">
        </div>

        <div class="form-group">
            <label>Plan de estudios:</label>
            <select name="carrera" class="select" required>
                <option value="">Seleccionar</option>
                @foreach($planes as $p)
                    <option value="{{ $p->idPlanDeEstudios }}"
                        {{ old('carrera') == $p->idPlanDeEstudios ? 'selected' : '' }}>
                        {{ $p->nombrePlan }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ================= ACCESO ================= --}}
        <h3 class="subtitulo-form">Acceso al sistema</h3>

        <div class="form-group">
            <label>Nombre de usuario:</label>
            <input type="text" name="nombreUsuario" class="input-mediano"
                   value="{{ old('nombreUsuario') }}" required>
        </div>

        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" name="password" class="input-chico" required>
        </div>

        <div class="form-group">
            <label>Confirmar contraseña:</label>
            <input type="password" name="password_confirmation"
                   class="input-chico" required>
        </div>

        {{-- BOTONES --}}
        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Guardar</button>
            <a href="{{ route('apartadoEstudiantes') }}"
               class="btn-boton-formulario btn-cancelar">
                Cancelar
            </a>
        </div>

    </form>
</main>

</body>
</html>
