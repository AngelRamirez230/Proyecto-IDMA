<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de estudiante</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <form action="{{ route('inicio')}}" method="POST" class="formulario">
    @csrf

        <div class="form-group">
            <label for="matriculaNumerica">Mátricula númerica:</label>
            <input type="text" id="matriculaNumerica" name="matriculaNumerica" class="input-mediano input-bloqueado" placeholder="" readonly>
        </div>  

        <div class="form-group">
            <label for="matriculaAlfanumerica">Mátricula alfanúmerica:</label>
            <input type="text" id="matriculaAlfanumerica" name="matriculaAlfanumerica" class="input-mediano input-bloqueado" placeholder="" readonly>
        </div>  

        <div class="form-group">
            <label for="carrera">Carrera:</label>
            <select id="carrera" name="carrera" class="select" required>
                <option value="" disabled selected>Seleccionar</option>
            </select>
        </div>


        <div class="form-group">
            <label for="primer_nombre">Primer nombre:</label>
            <input type="text" id="primer_nombre" name="primer_nombre" class="input-mediano" placeholder="Ingresa el primer nombre" required>
        </div>

        <div class="form-group">
            <label for="segundo_nombre">Segundo nombre:</label>
            <input type="text" id="segundo_nombre" name="segundo_nombre" class="input-mediano" placeholder="Ingresa el segundo nombre">
        </div>

        <div class="form-group">
            <label for="apellido_paterno">Apellido paterno:</label>
            <input type="text" id="apellido_paterno" name="apellido_paterno" class="input-mediano" placeholder="Ingresa el apellido paterno" required>
        </div>

        <div class="form-group">
            <label for="genero">Genero:</label>
            <select id="genero" name="genero" class="select" required>
                <option value="" disabled selected>Seleccionar</option>
            </select>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" class="input-chico" placeholder="Ingresa número de teléfono">
        </div>

        <div class="form-group">
            <label for="emailInstitucional">Correo institucional:</label>
            <input type="email" id="emailInstitucional" name="emailInstitucional" class="input-mediano" placeholder="ejemplo@idma.edu.mx" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" class="input-chico" placeholder="Escribe una contraseña" required>
        </div>

        <div class="form-group">
            <label for="nombreUsuario">Nombre de usuario:</label>
            <input type="text" id="nombreUsuario" name="nombreUsuario" class="input-mediano" placeholder="Ingresa el nombre de usuario" required>
        </div>

        <div class="form-group">
            <label for="fechaNacimiento">Fecha de nacimiento:</label>
            <input type="date" id="fechaNacimiento" name="fechaNacimiento" max="{{ date('Y-m-d') }}" class="input-date" required>
        </div>

        <div class="form-group">
            <label for="curp">Correo electrónico:</label>
            <input type="text" id="curp" name="curp" class="input-chico" placeholder="CURP" required>
        </div>

        <div class="form-group">
            <label for="rfc">Correo electrónico:</label>
            <input type="text" id="rfc" name="rfc" class="input-chico" placeholder="RFC">
        </div>

        <div class="form-group">
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" class="input-mediano" placeholder="ejemplo@correo.com" required>
        </div>

        <div class="form-group">
            <label for="entidad">Entidad:</label>
            <select id="entidad" name="entidad" class="select" required>
                <option value="" disabled selected>Seleccionar</option>
            </select>
        </div>

        <div class="form-group">
            <label for="municipio">Municipio:</label>
            <select id="municipio" name="municipio" class="select" required>
                <option value="" disabled selected>Seleccionar</option>
            </select>
        </div>

        <div class="form-group">
            <label for="localidad">Localidad:</label>
            <select id="localidad" name="localidad" class="select" required>
                <option value="" disabled selected>Seleccionar</option>
            </select>
        </div>

        <div class="form-group">
            <label for="codigoPostal">Código postal:</label>
            <input type="text" id="codigoPostal" name="codigoPostal" class="input-chico" placeholder="Ingresa el código postal">
        </div>

        <div class="form-group">
            <label for="calle">Calle:</label>
            <input type="text" id="calle" name="calle" class="input-grande" placeholder="Ingresa el nombre de la calle">
        </div>

        <div class="form-group">
            <label for="numeroExterior">Número exterior:</label>
            <input type="text" id="numeroExterior" name="numeroExterior" class="input-chico" placeholder="Número exterior">
        </div>

        <div class="form-group">
            <label for="numeroInterior">Numero interior:</label>
            <input type="text" id="numeroInterior" name="numeroInterior" class="input-chico" placeholder="Número interior">
        </div>

        <div class="form-group">
            <label for="colonia">Colonia:</label>
            <input type="text" id="colonia" name="colonia" class="input-mediano" placeholder="Ingresa el nombre de la colonia">
        </div>


        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Guardar</button>
            <a href="{{ route('apartadoEstudiantes') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>
    </form>

</body>
</html>