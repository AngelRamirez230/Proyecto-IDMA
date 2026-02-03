<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de reportes financieros</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="apartado-general">
        <a href="{{ route('eleccionFechas', 'aprobados') }}" class="btn-boton btn-pagos-aprobados">Pagos aprobados</a>
        <a href="{{ route('eleccionFechas', 'pendientes') }}" class="btn-boton btn-pagos-pendientes">Pagos pendientes</a>
        <a href="{{ route('eleccionFechas', 'rechazados') }}" class="btn-boton btn-pagos-rechazados">Pagos rechazados</a>
        <a href="{{ route('kardex.seleccionar.estudiante') }}" class="btn-boton btn-Kardex">Kárdex</a>
    </main>

</body>
</html>