<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de usuarios</title>
    @vite(['resources/css/app.css',])
</head>
<body>
    @include('layouts.barraNavegacion')
    <main class="apartado-general">
        <a href="{{ route('altaUsuarios') }}" class="btn-boton btn-alta-usuario">Alta de usuario</a>
        <a href="#" class="btn-boton btn-consulta-usuario">Consulta de usuario</a>
    </main>

</body>
</html>
