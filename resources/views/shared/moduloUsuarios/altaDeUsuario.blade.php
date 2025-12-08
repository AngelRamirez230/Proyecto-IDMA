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

    {{-- FORMULARIO DE ALTA DE USUARIO --}}
    <form action="{{ route('usuarios.store') }}" method="POST" class="formulario">
        @csrf

        {{-- PRIMER NOMBRE --}}
        <div class="form-group">
            <label for="primer_nombre">Primer nombre:</label>
            <input type="text" id="primer_nombre" name="primer_nombre" 
                   class="input-mediano" placeholder="Ingresa el primer nombre" 
                   value="{{ old('primer_nombre') }}" required>
        </div>

        {{-- SEGUNDO NOMBRE --}}
        <div class="form-group">
            <label for="segundo_nombre">Segundo nombre:</label>
            <input type="text" id="segundo_nombre" name="segundo_nombre" 
                   class="input-mediano" placeholder="Ingresa el segundo nombre"
                   value="{{ old('segundo_nombre') }}">
        </div>

        {{-- PRIMER APELLIDO --}}
        <div class="form-group">
            <label for="primer_apellido">Primer apellido:</label>
            <input type="text" id="primer_apellido" name="primer_apellido" 
                   class="input-mediano" placeholder="Ingresa el primer apellido" 
                   value="{{ old('primer_apellido') }}" required>
        </div>

        {{-- SEGUNDO APELLIDO --}}
        <div class="form-group">
            <label for="segundo_apellido">Segundo apellido:</label>
            <input type="text" id="segundo_apellido" name="segundo_apellido" 
                   class="input-mediano" placeholder="Ingresa el segundo apellido"
                   value="{{ old('segundo_apellido') }}">
        </div>

        {{-- SEXO --}}
        <div class="form-group">
            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" class="select" required>
                <option value="" disabled selected>Seleccionar</option>

                {{-- SI YA CARGAS EL CATÁLOGO DESDE EL CONTROLADOR --}}
                @if(isset($sexos))
                    @foreach($sexos as $sexo)
                        <option value="{{ $sexo->idSexo }}">{{ $sexo->nombreSexo }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        {{-- TELÉFONO --}}
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" 
                   class="input-chico" placeholder="Ingresa número de teléfono"
                   value="{{ old('telefono') }}">
        </div>

        {{-- CORREO INSTITUCIONAL --}}
        <div class="form-group">
            <label for="emailInstitucional">Correo institucional:</label>
            <input type="email" id="emailInstitucional" name="emailInstitucional" 
                   class="input-mediano" placeholder="ejemplo@idma.edu.mx"
                   value="{{ old('emailInstitucional') }}">
        </div>

        {{-- CONTRASEÑA --}}
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" 
                   class="input-chico" placeholder="Escribe una contraseña" required>
        </div>

        {{-- NOMBRE DE USUARIO --}}
        <div class="form-group">
            <label for="nombreUsuario">Nombre de usuario:</label>
            <input type="text" id="nombreUsuario" name="nombreUsuario" 
                   class="input-mediano" placeholder="Ingresa el nombre de usuario" 
                   value="{{ old('nombreUsuario') }}" required>
        </div>

        {{-- FECHA DE NACIMIENTO --}}
        <div class="form-group">
            <label for="fechaNacimiento">Fecha de nacimiento:</label>
            <input type="date" id="fechaNacimiento" name="fechaNacimiento"
                   max="{{ date('Y-m-d') }}" class="input-date"
                   value="{{ old('fechaNacimiento') }}">
        </div>

        {{-- CURP --}}
        <div class="form-group">
            <label for="curp">CURP:</label>
            <input type="text" id="curp" name="curp" 
                   class="input-chico" placeholder="CURP"
                   value="{{ old('curp') }}">
        </div>

        {{-- RFC --}}
        <div class="form-group">
            <label for="rfc">RFC:</label>
            <input type="text" id="rfc" name="rfc" 
                   class="input-chico" placeholder="RFC"
                   value="{{ old('rfc') }}">
        </div>

        {{-- CORREO PERSONAL --}}
        <div class="form-group">
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" 
                   class="input-mediano" placeholder="ejemplo@correo.com"
                   value="{{ old('email') }}">
        </div>

        {{-- ENTIDAD --}}
        <div class="form-group">
            <label for="entidad">Entidad:</label>
            <select id="entidad" name="entidad" class="select" required>
                <option value="" disabled selected>Seleccionar</option>

                @if(isset($entidades))
                    @foreach($entidades as $ent)
                        <option value="{{ $ent->idEntidad }}">{{ $ent->nombreEntidad }}</option>
                    @endforeach
                @endif
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
            <input type="text" id="codigoPostal" name="codigoPostal" 
                   class="input-chico" placeholder="Código postal"
                   value="{{ old('codigoPostal') }}">
        </div>

        {{-- CALLE --}}
        <div class="form-group">
            <label for="calle">Calle:</label>
            <input type="text" id="calle" name="calle" 
                   class="input-grande" placeholder="Ingresa la calle"
                   value="{{ old('calle') }}">
        </div>

        {{-- NÚMERO EXTERIOR --}}
        <div class="form-group">
            <label for="numeroExterior">Número exterior:</label>
            <input type="text" id="numeroExterior" name="numeroExterior" 
                   class="input-chico" placeholder="Número exterior"
                   value="{{ old('numeroExterior') }}">
        </div>

        {{-- NÚMERO INTERIOR --}}
        <div class="form-group">
            <label for="numeroInterior">Número interior:</label>
            <input type="text" id="numeroInterior" name="numeroInterior" 
                   class="input-chico" placeholder="Número interior"
                   value="{{ old('numeroInterior') }}">
        </div>

        {{-- COLONIA --}}
        <div class="form-group">
            <label for="colonia">Colonia:</label>
            <input type="text" id="colonia" name="colonia" 
                   class="input-mediano" placeholder="Ingresa colonia"
                   value="{{ old('colonia') }}">
        </div>

        {{-- BOTONES --}}
        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Guardar</button>
            <a href="{{ route('apartadoUsuarios') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>

    </form>
</body>
</html>