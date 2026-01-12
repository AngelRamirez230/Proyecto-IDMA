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

    <form action="{{ route('concepto.update', $concepto->idConceptoDePago)}}" method="POST" class="formulario2">
    @csrf
    @method('PUT') 

        <h1 class="titulo-form2">Modificación de concepto de pago</h1>

        <div class="form-group2">
            <label for="nombreConcepto">Nombre del concepto de pago:</label>
            <input type="text" id="nombreConcepto" name="nombreConcepto" class="input-grande2 input-bloqueado2" value="{{ $concepto->nombreConceptoDePago }}" readonly>
        </div>

        <div class="form-group2">
            <label for="costo">Costo:</label>
            <input type="text" id="costo" name="costo" class="input-chico2" value="{{ $concepto->costo }}" >
            <span id="costoError" class="mensajeError"></span>
        </div>

        <div class="form-group2">
            <label for="unidad">Unidad:</label>
            <select id="unidad" name="unidad" class="select2" required>
                <option value="" disabled>Seleccionar</option>

                @foreach ($unidades as $u)
                    <option value="{{ $u->idTipoDeUnidad }}"
                        {{ $concepto->idUnidad == $u->idTipoDeUnidad ? 'selected' : '' }}>
                        {{ $u->nombreUnidad }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="form-group2">
            <button type="submit" name="accion" value="guardar" class="btn-boton-formulario2">Guardar cambios</button>
            <button type="submit"
                    name="accion"
                    value="Suspender/Habilitar"
                    class="btn-boton-formulario2">
                {{ $concepto->idEstatus == 1 ? 'Suspender' : 'Habilitar' }}
            </button>
            <a href="{{ route('consultaConcepto') }}" class="btn-boton-formulario2 btn-cancelar2">Cancelar</a>
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