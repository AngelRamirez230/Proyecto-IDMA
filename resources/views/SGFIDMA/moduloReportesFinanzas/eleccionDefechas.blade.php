<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elección de fechas</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @include('layouts.barraNavegacion')

    <form action="{{ route('reportes.vistaPrevia') }}" method="POST" class="formulario2">
        @csrf

        <!-- TIPO DE REPORTE -->
        <input type="hidden" name="tipo" value="{{ $tipo }}">

        <h1 class="titulo-form2">Elige las fechas para el reporte</h1>

        <div class="form-group2">
            <label for="fechaInicioReporte">Fecha de inicio del reporte:</label>
            <input 
                type="date" 
                id="fechaInicioReporte" 
                name="fechaInicioReporte" 
                max="{{ date('Y-m-d') }}" 
                class="input-chico2" 
                required
            >
        </div>

        <div class="form-group2">
            <label for="fechaFinalReporte">Fecha final del reporte:</label>
            <input 
                type="date" 
                id="fechaFinalReporte" 
                name="fechaFinalReporte" 
                class="input-chico2" 
                required
            >
        </div>

        <div class="form-group2">
            <button type="submit" class="btn-boton-formulario2">
                Generar reporte
            </button>

            <a href="{{ url('/apartadoReporteFinanzas') }}" 
               class="btn-boton-formulario2 btn-cancelar2">
                Cancelar
            </a>
        </div>

    </form>


    <script>
        const fechaInicio = document.getElementById('fechaInicioReporte');
        const fechaFinal  = document.getElementById('fechaFinalReporte');

        fechaInicio.addEventListener('change', function () {
            // La fecha final no puede ser menor a la de inicio
            fechaFinal.min = this.value;

            // Si ya había una fecha final menor, se limpia
            if (fechaFinal.value && fechaFinal.value < this.value) {
                fechaFinal.value = '';
            }
        });
    </script>

</body>
</html>
