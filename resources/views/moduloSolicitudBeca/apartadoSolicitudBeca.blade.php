<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de solicitudes de beca</title>
    @vite(['resources/css/solicituDeBeca/apartadoSolicitudesDeBeca.css', 'resources/css/barraNavegacion.css' ])
</head>
<body>
    @include('barraNavegacion')

    <main class="apartado-de-solicitudes">
        <button type="button" class="btn-solicitar-beca">Solicitar beca</button>
        <button type="button" class="btn-consulta-solicitud">Consultar solicitudes</button>
        
    </main>

</body>
</html>