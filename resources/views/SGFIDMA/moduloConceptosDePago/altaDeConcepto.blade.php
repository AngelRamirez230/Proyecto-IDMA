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

    <form action="{{ route('concepto.store') }}" method="POST" class="formulario2">
    @csrf

        <h1 class="titulo-form2">Alta de concepto de pago</h1>

        <div class="form-group2">
            <label for="nombreConcepto">Nombre del concepto de pago:</label>
            <input type="text" id="nombreConcepto" name="nombreConcepto" class="input-grande2" placeholder="Ingresa el nombre del concepto de pago" required>
        </div>

        <div class="form-group2">
            <label for="costo">Costo:</label>
            <input type="text" id="costo" name="costo" class="input-chico2" placeholder="Ingresa el costo" required>
            <span id="costoError" class="mensajeError"></span>
        </div>

        <div class="form-group2">
            <label for="unidad">Unidad:</label>
            <select id="unidad" name="unidad" class="select2" required>
                <option value="" disabled selected>Seleccionar</option>

                @foreach ($unidades as $u)
                    <option value="{{ $u->idTipoDeUnidad }}">
                        {{ $u->nombreUnidad }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="form-group2">
            <button type="submit" class="btn-boton-formulario2">Guardar</button>
            <a href="{{ route('apartadoConceptos') }}" class="btn-boton-formulario2 btn-cancelar2">Cancelar</a>
        </div>

        <Script>
        
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

                if (valor < 0) {
                    errorCosto.textContent = "El costo debe ser mayor o igual a 0.";
                    return false;
                }

                errorCosto.textContent = "";
                return true;
            }

        </Script>
    </form>


</body>
</html>