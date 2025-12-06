<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado planes de pago</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')
    <main class="apartado-general">
        <a href="{{route('altaPlan')}}" class="btn-boton btn-alta-plan">Crear plan de pago</a>
        <a href="{{route('consultaPlan')}}" class="btn-boton btn-consulta-plan">Consultar planes de pago</a>
    </main>

</body>
</html>
