<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elección de fechas</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <form action="#" method="POST" class="formulario2">
    @csrf

        <h1 class="titulo-form2">Elige las fechas para el reporte</h1>

        <div class="form-group2">
            <label for="fechaInicioReporte">Fecha de incio del reporte:</label>
            <input type="date" id="fechaInicioReporte" name="fechaInicioReporte" max="{{ date('Y-m-d') }}" class="input-chico2" required>
        </div>

        <div class="form-group2">
            <label for="fechaFinalReporte">Fecha final del reporte:</label>
            <input type="date" id="fechaFinalReporte" name="fechaFinalReporte" max="{{ date('Y-m-d') }}" class="input-chico2" required>
        </div>


        <div class="form-group2">
            <button type="submit" class="btn-boton-formulario2">Generar reporte</button>
            <a href="{{ route('apartadoReportesFinanzas')}}" class="btn-boton-formulario2 btn-cancelar2">Cancelar</a>
        </div>
    </form>

</body>
</html>