<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de conceptos de pago</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')
    
    <main class="apartado-general">
        @admin
        <a href="{{ route('altaConcepto')}}" class="btn-boton btn-alta-conceptos">Añadir un nuevo concepto de pago</a>
        <a href="{{ route('consultaConcepto')}}" class="btn-boton btn-consulta-conceptos">Consultar conceptos de pago</a>
        @endadmin

        @estudiante
        <a href="{{ route('consultaConcepto')}}" class="btn-boton btn-consulta-conceptos">Consultar conceptos de pago</a>
        @endestudiante
    </main>

</body>
</html>