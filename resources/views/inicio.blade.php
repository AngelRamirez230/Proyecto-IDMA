<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite(['resources/css/inicio.css'])
</head>
<body>

<!-- NAV -->
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

<!-- CONTENIDO -->
<div class="content">

    <img src="{{ asset('imagenes/ImagenDeFondo.png') }}" class="fondoInferior" alt="imagenSuperiorFondo">

    <div class="btn-grid">

        <!-- BOTÓN 1 -->
        <div class="card-btn">
            <img src="/imagenes/IconoInicioUsuarios.png" alt="">
            <span class="card-btn-title">Usuarios</span>
        </div>

        <!-- BOTÓN 2 -->
        <div class="card-btn">
            <img src="/imagenes/IconoInicioEstudiantes.png" alt="">
            <span class="card-btn-title">Estudiantes</span>
        </div>

        <!-- BOTÓN 3 -->
        <div class="card-btn">
            <img src="/imagenes/IconoInicioDocentes.png" alt="">
            <span class="card-btn-title">Docentes</span>
        </div>

        <!-- BOTÓN 4 -->
        <div class="card-btn">
            <img src="/imagenes/IconoInicioAsignaturas.png" alt="">
            <span class="card-btn-title">Asignaturas</span>
        </div>

        <!-- BOTÓN 5 -->
        <div class="card-btn">
            <img src="/imagenes/IconoInicioGrupos.png" alt="">
            <span class="card-btn-title">Grupos</span>
        </div>

        <!-- BOTÓN 6 -->
        <div class="card-btn">
            <img src="/imagenes/IconoInicioHorarios.png" alt="">
            <span class="card-btn-title">Horarios</span>
        </div>

        <!-- BOTÓN 7 -->
        <div class="card-btn">
            <img src="/imagenes/IconoInicioCalificaciones.png" alt="">
            <span class="card-btn-title">Calificaciones</span>
        </div>

        <!-- BOTÓN 8 -->
        <div class="card-btn">
            <img src="/imagenes/IconoInicioPlanesdepago.png" alt="">
            <span class="card-btn-title">Planes de pago</span>
        </div>

        <!-- BOTÓN 9 -->
        <div class="card-btn">
            <img src="/imagenes/IconoInicioBecas.png" alt="">
            <span class="card-btn-title">Becas</span>
        </div>

        <!-- BOTÓN 10 -->
        <div class="card-btn">
            <img src="/imagenes/IconoInicioPagos.png" alt="">
            <span class="card-btn-title">Pagos</span>
        </div>
        
        <!-- BOTÓN 11 -->
        <div class="card-btn">
            <img src="/imagenes/IconoInicioReportes.png" alt="">
            <span class="card-btn-title">Reportes</span>
        </div>
    </div>
</div>
</body>
</html>
