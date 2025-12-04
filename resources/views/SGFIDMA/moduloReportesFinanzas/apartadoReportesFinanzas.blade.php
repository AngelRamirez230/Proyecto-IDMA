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
        <a href="#" class="btn-boton btn-pagos-aprobados">Pagos aprobados</a>
        <a href="#" class="btn-boton btn-pagos-pendientes">Pagos pendientes</a>
        <a href="#" class="btn-boton btn-pagos-rechazados">Pagos rechazados</a>
        <a href="#" class="btn-boton btn-Kardex">Kárdex</a>
    </main>

</body>
</html>