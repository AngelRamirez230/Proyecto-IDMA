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

    @if (session('success'))
        <div class="popup-notificacion" id="popup">
            <div class="popup-contenido">
                <p>{{ session('success') }}</p>
                <button class="popup-boton" onclick="cerrarPopup()">Aceptar</button>
            </div>
        </div>
    @endif

    @if (session('popupError'))
        <div class="popup-notificacion" id="popup">
            <div class="popup-contenido" style="color: red;">
                <p>{{ session('popupError') }}</p>
                <button class="popup-boton" onclick="cerrarPopup()">Aceptar</button>
            </div>
        </div>
    @endif


    <form action="{{ route('guardarConcepto') }}" method="POST" class="formulario">
    @csrf

        <div class="form-group">
            <label for="nombreConcepto">Nombre del concepto de pago:</label>
            <input type="text" id="nombreConcepto" name="nombreConcepto" class="input-grande" placeholder="Ingresa el nombre del concepto de pago" required>
        </div>

        <div class="form-group">
            <label for="costo">Costo:</label>
            <input type="text" id="costo" name="costo" class="input-chico" placeholder="Ingresa el costo" required>
        </div>

        <div class="form-group">
            <label for="unidad">Unidad:</label>
            <select id="unidad" name="unidad" class="select" required>
                <option value="" disabled selected>Seleccionar</option>

                @foreach ($unidades as $u)
                    <option value="{{ $u->idTipoDeUnidad }}">
                        {{ $u->nombreUnidad }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Guardar</button>
            <a href="{{ route('apartadoConceptos') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>
    </form>

</body>
</html>