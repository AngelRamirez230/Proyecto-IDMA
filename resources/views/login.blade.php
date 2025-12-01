<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/login.css'])
</head>
<body>
    
    <div class="rectanguloAmarillo">
        <form class="formularioLogin">

            <div class="inputGroup">
                <i class="fa-solid fa-circle-user iconoInput"></i>
                <input type="text" id="usuario" name="usuario" placeholder="Matrícula o correo">
            </div>

            <div class="inputGroup">
                <i class="fa-solid fa-lock iconoInput"></i>
                <input type="password" id="password" name="password" placeholder="Contraseña">
            </div>

            
            <button type="submit" class="btnLogin">Ingresar</button>
            <button type="button" class="btnGoogle">Google</button>

        </form>
    </div>

    <img src="{{ asset('imagenes/LOGO_INTERIOR_IDMA.png') }}" class="imagenIDMA" alt="imagenidma">
    <img src="{{ asset('imagenes/ImagenDeFondo.png') }}" class="fondoSuperio" alt="imagenSuperiorFondo">
    
    <div class="rectanguloRojoFondoLogin"></div>

</body>
</html>
