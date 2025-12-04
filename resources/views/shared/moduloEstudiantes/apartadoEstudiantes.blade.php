<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de estudiantes</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')
    <main class="apartado-general">
        <a href="#" class="btn-boton btn-alta-estudiante">Alta de estudiante</a>
        <a href="#" class="btn-boton btn-consulta-estudiante">Consulta de estudiante</a>
        <a href="#" class="btn-boton btn-Asignar-plan">Asignar plan de pagos</a>
    </main>

</body>
</html>