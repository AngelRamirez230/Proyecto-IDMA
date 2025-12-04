<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')
<!-- CONTENIDO -->
<div class="content">

    <div class="btn-grid">

            <!-- BOTÓN 1 -->
        <a href="{{ route('apartadoUsuarios') }}" class="card-btn">
            <img src="/imagenes/IconoInicioUsuarios.png" alt="">
            <span class="card-btn-title">Usuarios</span>
        </a>

        <a href="{{ route('apartadoEstudiantes') }}" class="card-btn">
            <img src="/imagenes/IconoInicioEstudiantes.png" alt="">
            <span class="card-btn-title">Estudiantes</span>
        </a>

        <a href="#" class="card-btn">
            <img src="/imagenes/IconoInicioDocentes.png" alt="">
            <span class="card-btn-title">Docentes</span>
        </a>

        <a href="#" class="card-btn">
            <img src="/imagenes/IconoInicioAsignaturas.png" alt="">
            <span class="card-btn-title">Asignaturas</span>
        </a>

        <a href="#" class="card-btn">
            <img src="/imagenes/IconoInicioGrupos.png" alt="">
            <span class="card-btn-title">Grupos</span>
        </a>

        <a href="#" class="card-btn">
            <img src="/imagenes/IconoInicioHorarios.png" alt="">
            <span class="card-btn-title">Horarios</span>
        </a>

        <a href="#" class="card-btn">
            <img src="/imagenes/IconoInicioCalificaciones.png" alt="">
            <span class="card-btn-title">Calificaciones</span>
        </a>

        <a href="{{ route('apartadoPlanDePago') }}" class="card-btn">
            <img src="/imagenes/IconoInicioPlanesdepago.png" alt="">
            <span class="card-btn-title">Planes de pago</span>
        </a>

        <a href="{{ route('apartadoBecas') }}" class="card-btn">
            <img src="/imagenes/IconoInicioBecas.png" alt="">
            <span class="card-btn-title">Becas</span>
        </a>

        <a href="{{ route('apartadoPagos') }}" class="card-btn">
            <img src="/imagenes/IconoInicioPagos.png" alt="">
            <span class="card-btn-title">Pagos</span>
        </a>

        <a href="{{ route('apartadoReportesFinanzas') }}" class="card-btn">
            <img src="/imagenes/IconoInicioReportes.png" alt="">
            <span class="card-btn-title">Reportes</span>
        </a>


        <a href="{{ route('apartadoConceptos') }}" class="card-btn">
            <img src="/imagenes/IconoConceptodepago.png" alt="">
            <span class="card-btn-title">Conceptos de pago</span>
        </a>

        <a href="#" class="card-btn">
            <img src="/imagenes/IconoBitacorasdelsistema.png" alt="">
            <span class="card-btn-title">Bítacoras del sistema</span>
        </a>
        
    </div>
</div>
</body>
</html>
