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

        {{-- TODOS --}}
        <li class="subnav-item">
            <a href="{{ route('inicio') }}">Inicio</a>
        </li>

        {{-- USUARIOS --}}
        @admin
        <li class="subnav-item">
            <a href="{{ route('apartadoUsuarios') }}">Usuarios</a>
        </li>
        @endadmin

        {{-- ESTUDIANTES --}}
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11))
        <li class="subnav-item">
            <a href="{{ route('apartadoEstudiantes') }}">Estudiantes</a>
        </li>
        @endif

        {{-- DOCENTES --}}
        @admin
        <li class="subnav-item"><a href="#">Docentes</a></li>
        @endadmin

        {{-- ASIGNATURAS --}}
        @admin
        <li class="subnav-item">
            <a href="{{ route('apartadoAsignaturas') }}">Asignaturas</a>
        </li>
        @endadmin

        {{-- GRUPOS --}}
        @php
            $puedeVerGrupos = auth()->check()
                && in_array((int) auth()->user()->idtipoDeUsuario, [1, 2], true);
        @endphp
        @if($puedeVerGrupos)
            <li class="subnav-item"><a href="{{ route('apartadoGrupos') }}">Grupos</a></li>
        @endif

        {{-- HORARIOS --}}
        @admin
        <li class="subnav-item"><a href="#">Horarios</a></li>
        @endadmin

        {{-- CALIFICACIONES --}}
        @admin
        <li class="subnav-item"><a href="#">Calificaciones</a></li>
        @endadmin

        {{-- BECAS --}}
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11) || Auth::user()->estudiante)
        <li class="subnav-item">
            <a href="{{ route('apartadoBecas') }}">Becas</a>
        </li>
        @endif

        {{-- PAGOS --}}
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11) || Auth::user()->estudiante)
        <li class="subnav-item">
            <a href="{{ route('apartadoPagos') }}">Pagos</a>
        </li>
        @endif

        {{-- CONCEPTOS DE PAGO --}}
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11) || Auth::user()->estudiante)
        <li class="subnav-item">
            <a href="{{ route('apartadoConceptos') }}">Conceptos de pago</a>
        </li>
        @endif

        {{-- PLANES DE PAGO --}}
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11))
        <li class="subnav-item">
            <a href="{{ route('apartadoPlanDePago') }}">Planes de pago</a>
        </li>
        @endif

        {{-- REPORTES --}}
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11))
        <li class="subnav-item">
            <a href="{{ route('apartadoReportes') }}">Reportes</a>
        </li>
        @endif

        {{-- BITÁCORAS --}}
        @admin
        <li class="subnav-item">
            <a href="{{ route('apartadoBitacoras') }}">Bítacoras del sistema</a>
        </li>
        @endadmin

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

    function cerrarPopupGeneracion() {
        document.getElementById('popupGeneracion').style.display = 'none';
    }

    </script>
