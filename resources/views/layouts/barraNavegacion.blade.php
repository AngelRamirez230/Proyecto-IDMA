@vite(['resources/css/app.css'])
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
<div class="navbar">
    <div class="nav-left">
        <img src="/imagenes/LogoIDMABlanco.png" alt="Logo" class="nav-logo">
    </div>

    <form method="POST" action="{{ route('logout') }}" class="nav-right">
        @csrf
        <button type="submit" class="logout-btn">
            <img src="/imagenes/IconoCerrarSesionNav.png" alt="icon" class="logout-icon">
            <span>Cerrar sesión</span>
        </button>
    </form>
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
        <li class="subnav-item"><a href="{{ route('apartadoReportes') }}">Reportes</a></li>
        <li class="subnav-item"><a href="{{ route('apartadoConceptos') }}">Conceptos de pago</a></li>
        <li class="subnav-item"><a href="{{ route('apartadoBitacoras') }}">Bítacoras del sistema</a></li>
    </ul>
</div>

<img src="{{ asset('imagenes/ImagenDeFondo.png') }}" class="fondoInferior" alt="imagenInferior">

<div class="popup-confirmacion" id="popupConfirmacion">
    <div class="popup-contenido">
        <p id="mensajeConfirmacion">¿Seguro?</p>
        <div class="popup-botones">
            <button class="btn-confirmar" onclick="confirmarEliminacion()">Eliminar</button>
            <button class="btn-cancelar-confirmacion" onclick="cerrarPopupConfirmacion()">Cancelar</button>
        </div>
    </div>
</div>

<script>
    function cerrarPopup() {
        document.getElementById('popup').style.display = 'none';
    }

    let formularioAEliminar = null;

    function cerrarPopupConfirmacion() {
        document.getElementById('popupConfirmacion').style.display = 'none';
        formularioAEliminar = null;
    }

    // Enviar el formulario real DELETE
    function confirmarEliminacion() {
        if (formularioAEliminar) {
            formularioAEliminar.submit();
         }
    }
    </script>