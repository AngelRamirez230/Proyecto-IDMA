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

    <form id="formBeca" action="{{ route('becas.store') }}" method="POST" class="formulario">
    @csrf

        <div class="form-group">
            <label for="nombreBeca">Nombre de Beca:</label>
            <input type="text" id="nombreBeca" name="nombreBeca" class="input-grande" placeholder="Ingresa el nombre de la beca" required>
        </div>

        <div class="form-group">
            <label for="porcentajeBeca">Porcentaje de descuento:</label>
            <input type="text" id="porcentajeBeca" name="porcentajeBeca" class="input-chico" placeholder="Porcentaje de descuento" required>
            <span id="porcentajeError" class="mensajeError"></span>
        </div>


        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Guardar</button>
            <a href="{{ route('apartadoBecas') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>
    </form>

    <script>
        const input = document.getElementById('porcentajeBeca');
        const error = document.getElementById('porcentajeError');
        const form = document.getElementById('formBeca');

        // Validar mientras escribe
        input.addEventListener('input', function () {
            validarPorcentaje();
        });

        // Validar al enviar el formulario
        form.addEventListener('submit', function (e) {
            if (!validarPorcentaje()) {
                e.preventDefault(); // Evita enviar
            }
        });

        function validarPorcentaje() {
            const valor = input.value;

            if (valor === "" || isNaN(valor)) {
                error.textContent = "Debes ingresar un número válido.";
                return false;
            }

            if (valor < 1 || valor > 100) {
                error.textContent = "El porcentaje debe estar entre 1 y 100.";
                return false;
            }

            error.textContent = "";
            return true;
        }
    </script>



</body>
</html>