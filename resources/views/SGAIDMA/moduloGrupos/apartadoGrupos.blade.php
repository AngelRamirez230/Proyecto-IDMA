<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de Grupos</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    @include('layouts.barraNavegacion')

    <main class="apartado-general">
        <a href="{{ route('altaGrupo') }}" class="btn-boton btn-alta-usuario">Alta de grupo</a>
        <a href="{{ route('consultaGrupo') }}" class="btn-boton btn-consulta-usuario">Consulta de grupo</a>
    </main>
</body>
</html>
