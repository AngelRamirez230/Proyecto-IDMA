<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de reportes</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="apartado-general">
        <a href="{{ route('apartadoReportesFinanzas') }}" class="btn-boton btn-reportes-financieros">Reportes financieros</a>
        <a href="#" class="btn-boton btn-reportes-academicos">Reportes académicos</a>

    </main>

</body>
</html>