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

    <form action="{{ route('becas.update', $beca->idBeca) }}" method="POST" class="formulario">
    @csrf
    @method('PUT') 

        <div class="form-group">
            <label for="nombreBeca">Nombre de Beca:</label>
             <input type="text" id="nombreBeca" name="nombreBeca" class="input-grande input-bloqueado" value="{{ $beca->nombreDeBeca }}" readonly>
        </div>

        <div class="form-group">
            <label for="porcentajeBeca">Porcentaje de descuento:</label>
            <div class="contenedor-input-icono">
                <input type="text" id="porcentajeBeca" name="porcentajeBeca" class="input-chico" value="{{ $beca->porcentajeDeDescuento }}">
                <img src="{{ asset('imagenes/IconoPorcentaje.png') }}" class="icono-input-img" alt="icono">
            </div>
            <span id="porcentajeError" class="mensajeError"></span>
        </div>
        


        <div class="form-group">
            <button type="submit"  name="accion" value="Suspender/Habilitar" class="btn-boton-formulario">Suspender/Habilitar</button>
            <button type="submit"  name="accion" value="guardar" class="btn-boton-formulario">Guardar cambios</button>
            <a href="{{ route('consultaBeca') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
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