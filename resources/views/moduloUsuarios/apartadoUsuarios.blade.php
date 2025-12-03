<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartado de usuarios</title>
    @vite(['resources/css/usuario/apartadoUsuario.css'])
</head>
<body>
    <div class="navbar">
        <!-- LOGOTIPO IZQUIERDA -->
        <div class="nav-left">
            <img src="/imagenes/LogoIDMABlanco.png" alt="Logo" class="nav-logo">
        </div class="nav-left">

        <!-- BOTÓN DERECHA -->
        <div class="nav-right">
            <button class="logout-btn">
                <img src="/imagenes/IconoCerrarSesionNav.png" alt="icon" class="logout-icon">
                <span>Cerrar sesión</span>
            </button>
        </div>
    </div>

    <!-- NAV SECUNDARIO -->
    <div class="subnav">
        <ul class="subnav-list">
            <li class="subnav-item">Inicio</li>
            <li class="subnav-item">Usuarios</li>
            <li class="subnav-item">Estudiantes</li>
            <li class="subnav-item">Docentes</li>
            <li class="subnav-item">Asignaturas</li>
            <li class="subnav-item">Grupos</li>
            <li class="subnav-item">Horarios</li>
            <li class="subnav-item">Calificaciones</li>
            <li class="subnav-item">Planes de pago</li>
            <li class="subnav-item">Becas</li>
            <li class="subnav-item">Pagos</li>
            <li class="subnav-item">Reportes</li>
        </ul>
    </div>

    <main class="apartado-de-usuarios">
        <button type="button" class="btn-alta-usuario">Alta de usuario</button>
        <button type="button" class="btn-consulta-usuario">Consulta de usuario</button>
        <img src="{{ asset('imagenes/ImagenDeFondo.png') }}" class="fondoInferior" alt="imagenInferior">
    
    </main>

</body>
</html>
