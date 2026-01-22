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
        @if(auth()->check() && (int)auth()->user()->idtipoDeUsuario === 1)
            <a href="#" class="btn-boton btn-alta-usuario">Alta de horario</a>
        @endif
        <a href="#" class="btn-boton btn-consulta-usuario">Consulta de horarios</a>
    </main>
</body>
</html>