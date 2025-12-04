@vite(['resources/css/app.css'])
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
        <li class="subnav-item"><a href="{{ route('inicio') }}">Inicio</a></li>
        <li class="subnav-item"><a href="{{ route('apartadoUsuarios') }}">Usuarios</a></li>
        <li class="subnav-item"><a href="{{ route('apartadoEstudiantes') }}">Estudiantes</a></li>
        <li class="subnav-item"><a href="#">Docentes</a></li>
        <li class="subnav-item"><a href="#">Asignaturas</a></li>
        <li class="subnav-item"><a href="#">Grupos</a></li>
        <li class="subnav-item"><a href="#">Horarios</a></li>
        <li class="subnav-item"><a href="#">Calificaciones</a></li>
        <li class="subnav-item"><a href="{{ route('apartadoPlanDePago') }}">Planes de pago</a></li>
        <li class="subnav-item"><a href="{{ route('apartadoBecas') }}">Becas</a></li>
        <li class="subnav-item"><a href="{{ route('apartadoPagos') }}">Pagos</a></li>
        <li class="subnav-item"><a href="{{ route('apartadoReportesFinanzas') }}">Reportes</a></li>
        <li class="subnav-item"><a href="{{ route('apartadoConceptos') }}">Conceptos de pago</a></li>
        <li class="subnav-item"><a href="#">Bítacoras del sistema</a></li>
    </ul>
</div>

<img src="{{ asset('imagenes/ImagenDeFondo.png') }}" class="fondoInferior" alt="imagenInferior">