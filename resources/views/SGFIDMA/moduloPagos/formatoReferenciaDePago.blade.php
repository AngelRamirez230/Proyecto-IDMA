<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Referencia de Pago</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        .box {
            border: 1px solid #000;
            padding: 20px;
        }
        .referencia {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>

    <h2>Referencia de Pago</h2>

    <div class="box">
        <p><strong>Matrícula:</strong> {{ $matricula }}</p>
        <p><strong>Fecha de generación:</strong> {{ $fecha }}</p>

        <hr>

        <p><strong>Referencia bancaria:</strong></p>
        <p class="referencia">{{ $referencia }}</p>
    </div>

</body>
</html>
