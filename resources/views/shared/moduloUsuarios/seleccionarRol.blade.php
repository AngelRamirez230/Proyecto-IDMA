<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar rol</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <div class="seleccionarRol">
        <h2>Seleccionar rol</h2>

        <!-- Rol: Administrador (id = 1) -->
        <a href="{{ route('usuarios.create', ['rol' => 1]) }}" 
           class="btn-seleccionarRol">
           Administrador
        </a>

        <!-- Rol: Empleado (id = 2) -->
        <a href="{{ route('usuarios.create', ['rol' => 2]) }}"
           class="btn-seleccionarRol">
           Empleado
        </a>

        <!-- Rol: Docente (id = 3) -->
        <a href="{{ route('usuarios.create', ['rol' => 3]) }}"
           class="btn-seleccionarRol">
           Docente
        </a>

        <!-- Rol: Estudiante (id = 4) -->
        <a href="{{ route('usuarios.create', ['rol' => 4]) }}" 
           class="btn-seleccionarRol">
           Estudiante
        </a>

    </div>

</body>
</html>
