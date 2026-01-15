<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de asignaturas</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="apartado-general">
        @if(!empty($puedeAlta))
            <a href="{{ route('altaAsignatura') }}" class="btn-boton btn-alta-usuario">Alta de asignatura</a>
        @endif
        <a href="{{ route('consultaAsignatura') }}" class="btn-boton btn-consulta-usuario">Consulta de asignatura</a>
    </main>
</body>
</html>
