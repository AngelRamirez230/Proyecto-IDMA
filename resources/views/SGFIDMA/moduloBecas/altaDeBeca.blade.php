<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de beca</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <form action="{{ route('inicio')}}" method="POST" class="formulario">
    @csrf

        <div class="form-group">
            <label for="nombreBeca">Nombre de Beca:</label>
            <input type="text" id="nombreBeca" name="nombreBeca" class="input-grande" placeholder="Ingresa el nombre de la beca" required>
        </div>

        <div class="form-group">
            <label for="porcentajeBeca">Porcentaje de descuento:</label>
            <input type="text" id="porcentajeBeca" name="porcentajeBeca" class="input-chico" placeholder="Porcentaje de descuento" required>
        </div>


        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Guardar</button>
            <a href="{{ route('apartadoBecas') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>
    </form>

</body>
</html>