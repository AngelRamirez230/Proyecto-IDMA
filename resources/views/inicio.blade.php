<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    @vite(['resources/css/inicio.css', 'resources/css/barraNavegacion.css'])
</head>
<body>
    @include('barraNavegacion')
<!-- CONTENIDO -->
<div class="content">

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
