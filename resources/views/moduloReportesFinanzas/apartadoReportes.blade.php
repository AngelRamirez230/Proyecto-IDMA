<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de reportes</title>
    @vite(['resources/css/reporteFinanzas/apartadoReporte.css', 'resources/css/barraNavegacion.css' ])
</head>
<body>
    @include('barraNavegacion')

    <main class="apartado-de-reportes">
        <button type="button" class="btn-pagos-aprobados">Pagos aprobados</button>
        <button type="button" class="btn-pagos-pendientes">Pagos pendientes</button>
        <button type="button" class="btn-pagos-rechazados">Pagos rechazados</button>
        <button type="button" class="btn-Kardex">Kárdex</button>
        
    </main>

</body>
</html>