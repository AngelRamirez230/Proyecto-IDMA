<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    @vite(['resources/css/app.css'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    @if(session('timeout'))
        <div class="popup-notificacion" id="popupTimeout">
            <div class="popup-contenido">
                <p>{{ session('timeout') }}</p>
                <button class="popup-boton" onclick="document.getElementById('popupTimeout').style.display='none'">Aceptar</button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="popup-notificacion" id="popupError">
            <div class="popup-contenido">
                {{-- Recorremos todos los errores --}}
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
                <button class="popup-boton" onclick="document.getElementById('popupError').style.display='none'">Aceptar</button>
            </div>
        </div>
    @endif

    <img src="{{ asset('imagenes/ImagenDeFondo.png') }}" class="fondoSuperior" alt="imagenSuperiorFondo">
    
    <div class="rectanguloRojoFondoLogin"></div>

    <div class="rectanguloAmarillo">
        <img src="{{ asset('imagenes/LOGO_INTERIOR_IDMA.png') }}" class="imagenIDMA" alt="imagenidma">
        
        <form class="formularioLogin" method="POST" action="{{ route('login.process') }}">
            @csrf
            

            <div class="cajaDeTexto">
                <img src="{{ asset('imagenes/IconoLoginUsuario.png') }}" class="iconoLoginUsuario" alt="iconoLoginUsuario">
                <input type="text" id="usuario" name="usuario" placeholder="Matrícula o correo" required>
            </div>

            <div class="cajaDeTexto">
                <img src="{{ asset('imagenes/IconoLoginContraseña.png') }}" class="iconoLoginContraseña" alt="iconoLoginContraseña">
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
            </div>

            
            <button type="submit" class="btnLogin">Ingresar</button>

            
            
            <button type="button" class="btnGoogle">
                <img src="{{ asset('imagenes/IconoGoogle.png') }}" class="iconoGoogle" alt="iconoGoogle">
                Google
            </button>
            
            

        </form>
    </div>

    <script>
        setTimeout(() => {
            const popup = document.getElementById('popupTimeout','popupError');
            if(popup) popup.style.display = 'none';
        }, 5000);
    </script>


</body>
</html>
