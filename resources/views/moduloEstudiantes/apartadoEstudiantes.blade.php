<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de estudiantes</title>
    @vite(['resources/css/estudiante/apartadoEstudiante.css', 'resources/css/barraNavegacion.css'])
</head>
<body>
    @include('barraNavegacion')
    <main class="apartado-de-estudiantes">
        <button type="button" class="btn-alta-estudiante">Alta de usuario</button>
        <button type="button" class="btn-consulta-estudiante">Consulta de usuario</button>
        <button type="button" class="btn-Asignar-plan">Asignar plan de pagos</button>
    </main>

</body>
</html>