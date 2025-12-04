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
        <button type="button" class="btn-alta-estudiante">Alta de estudiante</button>
        <button type="button" class="btn-consulta-estudiante">Consulta de estudiante</button>
        <button type="button" class="btn-Asignar-plan">Asignar plan de pagos</button>
    </main>

</body>
</html>