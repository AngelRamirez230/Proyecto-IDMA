<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    @vite(['resources/css/pages/login.css'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <img src="{{ asset('imagenes/ImagenDeFondo.png') }}" class="fondoSuperior" alt="imagenSuperiorFondo">
    
    <div class="rectanguloRojoFondoLogin"></div>

    <div class="rectanguloAmarillo">
        <img src="{{ asset('imagenes/LOGO_INTERIOR_IDMA.png') }}" class="imagenIDMA" alt="imagenidma">
        
        <form class="formularioLogin">
            

            <div class="cajaDeTexto">
                <img src="{{ asset('imagenes/IconoLoginUsuario.png') }}" class="iconoLoginUsuario" alt="iconoLoginUsuario">
                <input type="text" id="usuario" name="usuario" placeholder="Matrícula o correo">
            </div>

            <div class="cajaDeTexto">
                <img src="{{ asset('imagenes/IconoLoginContraseña.png') }}" class="iconoLoginContraseña" alt="iconoLoginContraseña">
                <input type="password" id="password" name="password" placeholder="Contraseña">
            </div>

            
            <button type="submit" class="btnLogin">Ingresar</button>

            
            
            <button type="button" class="btnGoogle">
                <img src="{{ asset('imagenes/IconoGoogle.png') }}" class="iconoGoogle" alt="iconoGoogle">
                Google
            </button>
            
            

        </form>
    </div>


</body>
</html>
