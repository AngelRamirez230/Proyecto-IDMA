<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de usuarios</title>
    @vite(['resources/css/planDePago/apartadoPlanDePago.css','resources/css/barraNavegacion.css'])
</head>
<body>
    @include('barraNavegacion')
    <main class="apartado-planes">
        <button type="button" class="btn-alta-plan">Crear plan de pago</button>
        <button type="button" class="btn-consulta-plan">Consultar planes de pago</button>
        <img src="{{ asset('imagenes/ImagenDeFondo.png') }}" class="fondoInferior" alt="imagenInferior">
    
    </main>

</body>
</html>
