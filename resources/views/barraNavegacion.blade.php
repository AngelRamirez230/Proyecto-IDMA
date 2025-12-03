@vite(['resources/css/barraNavegacion.css'])
<div class="navbar">
    <div class="nav-left">
        <img src="/imagenes/LogoIDMABlanco.png" alt="Logo" class="nav-logo">
    </div>

    <div class="nav-right">
        <button class="logout-btn">
            <img src="/imagenes/IconoCerrarSesionNav.png" alt="icon" class="logout-icon">
            <span>Cerrar sesión</span>
        </button>
    </div>
</div>

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

<img src="{{ asset('imagenes/ImagenDeFondo.png') }}" class="fondoInferior" alt="imagenInferior">