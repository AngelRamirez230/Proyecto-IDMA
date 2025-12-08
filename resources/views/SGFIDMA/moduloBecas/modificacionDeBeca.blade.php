<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificación de beca</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <form action="{{ route('inicio')}}" method="POST" class="formulario">
    @csrf

        <div class="form-group">
            <label for="nombreBeca">Nombre de Beca:</label>
             <input type="text" id="nombreBeca" name="nombreBeca" class="input-grande input-bloqueado" value="{{ $beca->nombreDeBeca }}" readonly>
        </div>

        <div class="form-group">
            <label for="porcentajeBeca">Porcentaje de descuento:</label>
            <input type="text" id="porcentajeBeca" name="porcentajeBeca" class="input-chico" value="{{ $beca->porcentajeDeDescuento }}">
        </div>


        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Suspender/Habilitar</button>
            <button type="submit" class="btn-boton-formulario">Guardar cambios</button>
            <a href="{{ route('consultaBeca') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>
    </form>

</body>
</html>