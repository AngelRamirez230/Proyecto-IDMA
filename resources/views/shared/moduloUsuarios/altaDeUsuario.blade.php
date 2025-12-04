<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de usuario</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <form action="#" method="POST" class="formulario-alta-usuario">
    @csrf

        <div class="form-group">
            <label for="primer_nombre">Primer nombre:</label>
            <input type="text" id="primer_nombre" name="primer_nombre" placeholder="Ingresa el primer nombre" required>
        </div>

        <div class="form-group">
            <label for="segundo_nombre">Segundo nombre:</label>
            <input type="text" id="segundo_nombre" name="segundo_nombre" placeholder="Ingresa el segundo nombre">
        </div>

        <div class="form-group">
            <label for="apellido_paterno">Apellido paterno:</label>
            <input type="text" id="apellido_paterno" name="apellido_paterno" placeholder="Ingresa el apellido paterno" required>
        </div>

        <div class="form-group">
            <label for="apellido_materno">Apellido materno:</label>
            <input type="text" id="apellido_materno" name="apellido_materno" placeholder="Ingresa el apellido materno">
        </div>

        <div class="form-group">
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" placeholder="Escribe una contraseña" required>
        </div>

        <button type="submit" class="btn-boton">Crear Usuario</button>
    </form>
</body>
</html>