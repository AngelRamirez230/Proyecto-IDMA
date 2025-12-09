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


    <form action="{{ route('concepto.store') }}" method="POST" class="formulario">
    @csrf

        <div class="form-group">
            <label for="nombreConcepto">Nombre del concepto de pago:</label>
            <input type="text" id="nombreConcepto" name="nombreConcepto" class="input-grande" placeholder="Ingresa el nombre del concepto de pago" required>
        </div>

        <div class="form-group">
            <label for="costo">Costo:</label>
            <input type="text" id="costo" name="costo" class="input-chico" placeholder="Ingresa el costo" required>
            <span id="costoError" class="mensajeError"></span>
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

        <Script>
            function cerrarPopup() {
                document.getElementById('popup').style.display = 'none';
            }


            const inputCosto = document.getElementById('costo');
            const errorCosto = document.getElementById('costoError');
            const form = document.querySelector('form');

            inputCosto.addEventListener('input', validarCosto);
            form.addEventListener('submit', function (e) {
                if (!validarCosto()) {
                    e.preventDefault(); 
                }
            });

            function validarCosto() {
                const valor = inputCosto.value;

                if (valor === "" || isNaN(valor)) {
                    errorCosto.textContent = "Debes ingresar un número válido.";
                    return false;
                }

                if (valor < 1) {
                    errorCosto.textContent = "El costo debe ser mayor o igual a 1.";
                    return false;
                }

                errorCosto.textContent = "";
                return true;
            }

        </Script>
    </form>


</body>
</html>