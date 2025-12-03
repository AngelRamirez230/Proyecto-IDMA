<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de conceptos de pago</title>
    @vite(['resources/css/conceptoDePago/apartadoConcepto.css' , 'resources/css/barraNavegacion.css'])
</head>
<body>
    @include('barraNavegacion')
    
    <main class="apartado-de-conceptos">
        <button type="button" class="btn-alta-conceptos">Añadir concepto de pago</button>
        <button type="button" class="btn-consulta-conceptos">Consultar conceptos de pago</button>
        
    </main>

</body>
</html>