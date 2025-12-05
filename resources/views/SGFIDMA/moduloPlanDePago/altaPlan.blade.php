<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de concepto de pago</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <form action="{{ route('inicio')}}" method="POST" class="formulario">
    @csrf

        <div class="form-group">
            <label for="nombrePlan">Nombre del plan de pago:</label>
            <input type="text" id="nombrePlan" name="nombrePlan" class= input-grande placeholder="Ingresa el nombre del plan de pago" required>
        </div>

        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Guardar</button>
            <a href="{{ route('apartadoPlanDePago') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>
    </form>

</body>
</html>