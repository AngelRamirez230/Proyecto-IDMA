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

    <form action="#" method="POST" class="formulario">
    @csrf

        <div class="form-group">
            <label for="fechaInicioReporte">Fecha de incio del reporte:</label>
            <input type="date" id="fechaInicioReporte" name="fechaInicioReporte" max="{{ date('Y-m-d') }}" class="input-date" required>
        </div>

        <div class="form-group">
            <label for="fechaFinalReporte">Fecha final del reporte:</label>
            <input type="date" id="fechaFinalReporte" name="fechaFinalReporte" max="{{ date('Y-m-d') }}" class="input-date" required>
        </div>


        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Generar reporte</button>
            <a href="{{ route('apartadoReportesFinanzas')}}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>
    </form>

</body>
</html>