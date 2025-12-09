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

    <form id="formBeca" action="{{ route('becas.store') }}" method="POST" class="formulario">
    @csrf

        <div class="form-group">
            <label for="nombreBeca">Nombre de Beca:</label>
            <input type="text" id="nombreBeca" name="nombreBeca" class="input-grande" placeholder="Ingresa el nombre de la beca" required>
        </div>

        <div class="form-group input-con-icono">
            <label for="porcentajeBeca">Porcentaje de descuento:</label>
            <div class="contenedor-input-icono">
                <input type="text" id="porcentajeBeca" name="porcentajeBeca" class="input-chico" placeholder="Porcentaje de descuento" required>

                <img src="{{ asset('imagenes/IconoPorcentaje.png') }}" class="icono-input-img" alt="icono">
            </div>

             <span id="porcentajeError" class="mensajeError"></span>
        </div>


        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Guardar</button>
            <a href="{{ route('apartadoBecas') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>
    </form>

      <script>

        
        function cerrarPopup() {
            document.getElementById('popup').style.display = 'none';
        }
    
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