<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/login.css'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <img src="{{ asset('imagenes/ImagenDeFondo.png') }}" class="fondoSuperior" alt="imagenSuperiorFondo">
    
    <div class="rectanguloRojoFondoLogin"></div>

    <div class="rectanguloAmarillo">
        <img src="{{ asset('imagenes/LOGO_INTERIOR_IDMA.png') }}" class="imagenIDMA" alt="imagenidma">
        
        <form class="formularioLogin">
            

            <div class="cajaDeTexto">
                <i class="fa-solid fa-circle-user iconoInput"></i>
                <input type="text" id="usuario" name="usuario" placeholder="Matrícula o correo">
            </div>

            <div class="cajaDeTexto">
                <i class="fa-solid fa-lock iconoInput"></i>
                <input type="password" id="password" name="password" placeholder="Contraseña">
            </div>

            
            <button type="submit" class="btnLogin">Ingresar</button>

            
            
            <button type="button" class="btnGoogle">
                <i class="fa-brands fa-google iconoInput"></i> 
                Google
            </button>
            
            

        </form>
    </div>


</body>
</html>
