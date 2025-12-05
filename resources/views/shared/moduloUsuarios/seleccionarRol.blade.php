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

        <a href="{{route('altaUsuarios')}}" class="btn-seleccionarRol">Administrador</a>
        <a href="" class="btn-seleccionarRol">Docente</a>
        <a href="{{route('altaEstudiante')}}" class="btn-seleccionarRol">Estudiante</a>
        <a href="" class="btn-seleccionarRol">Empleado</a>

    </div>
    
</body>
</html>