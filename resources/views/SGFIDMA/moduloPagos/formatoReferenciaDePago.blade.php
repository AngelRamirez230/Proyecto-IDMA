<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Referencia de Pago</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;          /* 👈 letra más chica */
            text-transform: uppercase; /* 👈 TODO en mayúsculas */
        }
        .copia {
            border-bottom: 1px dashed #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        h2 {
            text-align: center;
            margin: 0;              /* quita TODO el margen */
            padding: 0;
            color: #79272C;
            line-height: 1;         /* reduce altura */
        }
        .linea {
            margin: 4px 0;
        }
        .referencia {
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
            letter-spacing: 1px;
        }
        .importe {
            font-size: 14px;
            font-weight: bold;
        }
        .titulo {
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
        }

        td {
            padding: 3px;
            line-height: 1.1;
        }
    </style>
</head>
<body>

@for ($i = 0; $i < 2; $i++)
<div class="copia">

    <h2>IDMA</h2>
    <div style="text-align: center; margin: 0;">
        <img src="{{ public_path('imagenes/LogoIDMAreferencia.png') }}"
            alt="Logo IDMA"
            style="width: 200px; display: block; margin: 10px auto 20px auto;">
    </div>

   <table width="100%" cellspacing="0" cellpadding="0"
       style="border-collapse: collapse; table-layout: fixed; margin-top:4px;">

        <tr>
            <!-- IZQUIERDA -->
            <td width="75%" style="border-top:1px solid #000;
                                border-left:1px solid #000;
                                border-bottom:1px solid #000;
                                border-right:0;
                                height:12px;
                                padding:3px;">
                <strong>NOMBRE DEL ESTUDIANTE:</strong> {{ $nombreCompleto }}
            </td>

            <!-- DERECHA (ABARCA TODOS LOS RENGLONES) -->
            <td width="25%" rowspan="6"
                style="border:1px solid #000;
                    vertical-align: top;
                    padding:4px;
                    line-height:1.1;">
                <strong>FECHA DE EMISIÓN:</strong><br>
                {{ $fechaEmision }}<br><br>

                <strong>FECHA LÍMITE DEL PAGO:</strong><br>
                {{ $fechaLimite }}
            </td>
        </tr>

        <tr>
            <td style="border-left:1px solid #000;
                    border-bottom:1px solid #000;
                    border-right:0;
                    height:12px;
                    padding:3px;">
                <strong>NO. MATRÍCULA:</strong> {{ $estudiante->matriculaAlfanumerica }}
            </td>
        </tr>

        <tr>
            <td style="border-left:1px solid #000;
                    border-bottom:1px solid #000;
                    border-right:0;
                    height:12px;"></td>
        </tr>

        <tr>
            <td style="border-left:1px solid #000;
                    border-bottom:1px solid #000;
                    border-right:0;
                    height:12px;
                    padding:3px;">
                <strong>NIVEL:</strong> LICENCIATURA
            </td>
        </tr>

        <tr>
            <td style="border-left:1px solid #000;
                    border-bottom:1px solid #000;
                    border-right:0;
                    height:12px;
                    padding:3px;">
                <strong>GENERACIÓN:</strong> 2022A
            </td>
        </tr>

        <tr>
            <td style="border-left:1px solid #000;
                    border-bottom:1px solid #000;
                    border-right:0;
                    height:12px;
                    padding:3px;">
                <strong>PERIODO ESCOLAR:</strong> 01
            </td>
        </tr>

    </table>





    <br>

    <p class="linea">
        <strong>DESCRIPCIÓN:</strong>
        {{ str_pad($concepto->idConceptoDePago, 2, '0', STR_PAD_LEFT) }}
        {{ $concepto->nombreConceptoDePago }}
    </p>

    <p class="linea"><strong>APORTACIÓN:</strong> {{ $concepto->descripcion ?? 'Mes en curso' }}</p>

    <p class="importe"><strong>IMPORTE:</strong>$ {{ number_format($concepto->costo, 2) }}</p>

    <br>

    <div class="referencia">
        NÚMERO DE REFERENCIA<br>
        PAGO DE SERVICIO {{ $referencia }}
    </div>

    <div class="titulo">
        {{ $i === 0 ? 'COPIA PARA EL ESTUDIANTE' : 'COPIA PARA EL IDMA' }}
    </div>

</div>
@endfor

</body>
</html>
