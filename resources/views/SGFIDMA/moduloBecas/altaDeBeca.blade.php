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

    <form id="formBeca" action="{{ route('becas.store') }}" method="POST" class="formulario2">
    @csrf

        <h1 class="titulo-form2">Alta de beca</h1>

        <div class="form-group2">
            <label for="nombreBeca">Nombre de Beca:</label>
            <input type="text" id="nombreBeca" name="nombreBeca" class="input-grande2" placeholder="Ingresa el nombre de la beca" required>
        </div>

        <div class="form-group2 input-con-icono">
            <label for="porcentajeBeca">Porcentaje de descuento:</label>
            <div class="contenedor-input-icono2">
                <input type="text" id="porcentajeBeca" name="porcentajeBeca" class="input-chico2" placeholder="Porcentaje de descuento" required>

                <img src="{{ asset('imagenes/IconoPorcentaje.png') }}" class="icono-input-img2" alt="icono">
            </div>

             <span id="porcentajeError" class="mensajeError"></span>
        </div>


        <div class="form-group2">
            <button type="submit" class="btn-boton-formulario2">Guardar</button>
            <a href="{{ route('apartadoBecas') }}" class="btn-boton-formulario2 btn-cancelar2">Cancelar</a>
        </div>
    </form>

      <script>
    
        const inputPorcentaje = document.getElementById('porcentajeBeca');
        const errorPorcentaje = document.getElementById('porcentajeError');
        const form = document.getElementById('formBeca');

        // Validar mientras escribe
        inputPorcentaje.addEventListener('input', validarPorcentaje);

        // Validar al enviar el formulario
        form.addEventListener('submit', function (e) {
            if (!validarPorcentaje()) {
                e.preventDefault(); 
            }
        });

        function validarPorcentaje() {
            const valor = inputPorcentaje.value;

            if (valor === "" || isNaN(valor)) {
                errorPorcentaje.textContent = "Debes ingresar un número válido.";
                return false;
            }

            if (valor < 1 || valor > 100) {
                errorPorcentaje.textContent = "El porcentaje debe estar entre 1 y 100.";
                return false;
            }

            errorPorcentaje.textContent = "";
            return true;
        }
    </script>

</body>

</html>