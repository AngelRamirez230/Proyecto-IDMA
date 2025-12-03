<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de usuarios</title>
    @vite(['resources/css/usuario/apartadoUsuario.css', 'resources/css/barraNavegacion.css'])
</head>
<body>
    @include('barraNavegacion')
    <main class="apartado-de-usuarios">
        <button type="button" class="btn-alta-usuario">Alta de usuario</button>
        <button type="button" class="btn-consulta-usuario">Consulta de usuario</button>
    
    </main>

</body>
</html>
