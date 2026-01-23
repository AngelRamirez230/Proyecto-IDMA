<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de Horarios</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    @include('layouts.barraNavegacion')

    <main class="apartado-general">
        @if(auth()->check() && (auth()->user()->esAdmin() || auth()->user()->esEmpleadoDe([2, 3, 4, 5, 6, 7])))
            <a href="{{ route('altaHorario') }}" class="btn-boton btn-alta-usuario">Alta de horario</a>
        @endif
        <a href="#" class="btn-boton btn-consulta-usuario">Consulta de horarios</a>
    </main>
</body>
</html>
