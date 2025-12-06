<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificación de Concepto de pago</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <form action="{{ route('inicio')}}" method="POST" class="formulario">
    @csrf

        <div class="form-group">
            <label for="nombreConcepto">Nombre del concepto de pago:</label>
            <input type="text" id="nombreConcepto" name="nombreConcepto" class="input-grande input-bloqueado" readonly>
        </div>

        <div class="form-group">
            <label for="costo">Costo:</label>
            <input type="text" id="costo" name="costo" class="input-chico" placeholder="Ingresa el costo">
        </div>

        <div class="form-group">
            <label for="unidad">Unidad:</label>
            <select id="unidad" name="unidad" class="select" required>
                <option value="" disabled selected>Seleccionar</option>
            </select>
        </div>


        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Suspender/Habilitar</button>
            <button type="submit" class="btn-boton-formulario">Guardar cambios</button>
            <a href="{{ route('apartadoConceptos') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>
    </form>

</body>
</html>