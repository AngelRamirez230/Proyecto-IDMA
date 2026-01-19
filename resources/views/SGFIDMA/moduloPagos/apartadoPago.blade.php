<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de pagos</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="apartado-general">
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11))
        <a href="{{route('consultaPagos')}}" class="btn-boton btn-consulta-pago">Consultar pagos</a>
        <a href="{{route('admin.pagos.create')}}" class="btn-boton btn-asignar-pago">Asignar pago a estudiante(s)</a>
        <a href="#" class="btn-boton btn-validar-pago">Validar pagos</a>
        @endif

        @estudiante
        <a href="{{route('consultaPagos')}}" class="btn-boton btn-consulta-pago">Consultar pagos</a>
        @endestudiante
        
        
    </main>

</body>
</html>