<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de becas</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    

    <main class="apartado-general">

        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11))
            <a href="{{ route('altaBeca')}}" class="btn-boton btn-alta-beca">Alta de beca</a>
            <a href="{{ route('consultaBeca')}}" class="btn-boton btn-consulta-beca">Consultar beca</a>
            <a href="{{ route('consultaSolicitudBeca')}}" class="btn-boton btn-consulta-solicitud">Consultar solicitudes</a>
        @endif

        @estudiante
            <a href="{{ route('consultaBeca')}}" class="btn-boton btn-consulta-beca">Consultar beca</a>
            <a href="{{ route('consultaSolicitudBeca')}}" class="btn-boton btn-consulta-solicitud">Consultar solicitudes</a>
        @endestudiante
        
    </main>

</body>
</html>