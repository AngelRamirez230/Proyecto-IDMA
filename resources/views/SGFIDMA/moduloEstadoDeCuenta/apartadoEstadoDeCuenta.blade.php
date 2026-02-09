<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de estados de cuenta</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="apartado-general">
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11))
            <a href="{{ route('estadosCuenta.seleccionarEstudiante') }}" class="btn-boton btn-consultar-estados-de-cuenta-estudiante">Consultar estados de cuenta por estudiante</a>
        @endif  
        
        @estudiante
            <a href="{{ route('estadoCuenta.miEstado') }}" class="btn-boton btn-consultar-estados-de-cuenta">Consulta tu estado de cuenta</a>
        @endestudiante

    </main>

</body>
</html>