<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de calificaciones</title>
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="apartado-general">
        <a href="{{ route('consultaCalificaciones') }}" class="btn-boton btn-consulta-usuario">Consulta de calificaciones</a>
        <a href="{{ route('calificaciones.edit', 1) }}" class="btn-boton btn-consulta-usuario">Editar calificaciones</a>
    </main>
</body>
</html>
