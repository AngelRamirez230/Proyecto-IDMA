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

    <form action="{{ route('concepto.update', $concepto->idConceptoDePago)}}" method="POST" class="formulario">
    @csrf
    @method('PUT') 

        <div class="form-group">
            <label for="nombreConcepto">Nombre del concepto de pago:</label>
            <input type="text" id="nombreConcepto" name="nombreConcepto" class="input-grande input-bloqueado" value="{{ $concepto->nombreConceptoDePago }}" readonly>
        </div>

        <div class="form-group">
            <label for="costo">Costo:</label>
            <input type="text" id="costo" name="costo" class="input-chico" value="{{ $concepto->costo }}" >
            <span id="costoError" class="mensajeError"></span>
        </div>

        <div class="form-group">
            <label for="unidad">Unidad:</label>
            <select id="unidad" name="unidad" class="select" required>
                <option value="" disabled>Seleccionar</option>

                @foreach ($unidades as $u)
                    <option value="{{ $u->idTipoDeUnidad }}"
                        {{ $concepto->idUnidad == $u->idTipoDeUnidad ? 'selected' : '' }}>
                        {{ $u->nombreUnidad }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="form-group">
            <button type="submit" name="accion" value="guardar" class="btn-boton-formulario">Guardar cambios</button>
            <button type="submit"
                    name="accion"
                    value="Suspender/Habilitar"
                    class="btn-boton-formulario">
                {{ $concepto->idEstatus == 1 ? 'Suspender' : 'Habilitar' }}
            </button>
            <a href="{{ route('consultaConcepto') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>
    </form>

    <script>
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
    </script>

</body>
</html>