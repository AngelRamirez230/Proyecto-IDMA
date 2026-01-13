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
        .linea-corte {
            position: fixed;
            top: 50%;
            left: 0;
            right: 0;
            border-top: 1px dashed #000;
        }
        h2 {
            text-align: center;
            margin: 0;              /* quita TODO el margen */
            padding: 0;
            color: #79272C;
            line-height: 1;         /* reduce altura */
            font-size: 20px;
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

        .titulo-copia {
            font-size: 10px;
            color: #C00000;
            text-align: left;
            margin: 0;
            padding-top: 2px;
        }

        .copia + .copia {
            margin-top: 40px;
        }
    </style>
</head>
<body>

<div class="linea-corte"></div>

@for ($i = 0; $i < 2; $i++)
<div class="copia">

    <h2>IDMA</h2>
    <div style="text-align: center; margin: 0;">
        <img src="{{ public_path('imagenes/LogoIDMAreferencia.png') }}"
            alt="Logo IDMA"
            style="width: 220px; display: block; margin: 5px auto 20px auto;">
    </div>

   <table width="100%" cellspacing="0" cellpadding="0"
       style="border-collapse: collapse; table-layout: fixed; margin-top:4px;">

        <tr>
            <!-- IZQUIERDA -->
            <td width="75%" style="border-top:1px solid #000;
                                border-left:1px solid #000;
                                border-bottom:1px solid #000;
                                border-right:0;
                                height:20px;
                                padding:3px;">
                <strong>NOMBRE DEL ESTUDIANTE:</strong> {{ $nombreCompleto }}
            </td>

            <!-- DERECHA (ABARCA TODOS LOS RENGLONES) -->
            <td width="25%" rowspan="6"
                style="border:1px solid #000;
                    vertical-align: middle;
                    padding:4px;
                    line-height:1.3;">

                <div style="text-align:center;">

                    <strong>FECHA DE EMISIÓN:</strong><br>
                    {{ $fechaEmision }}<br><br>

                    <strong>FECHA LÍMITE DEL PAGO:</strong><br>
                    <span style="color:#C00000; font-weight:bold;">
                        {{ $fechaLimite }}
                    </span>
                </div>
            </td>
        </tr>

        <tr>
            <td style="border-left:1px solid #000;
                    border-bottom:1px solid #000;
                    border-right:0;
                    height:18px;
                    padding:3px;">
                <strong>NO. MATRÍCULA:</strong> {{ $estudiante->matriculaAlfanumerica }}
            </td>
        </tr>

        <tr>
            <td style="border-left:1px solid #000;
                    border-bottom:1px solid #000;
                    border-right:0;
                    height:18px;"></td>
        </tr>

        <tr>
            <td style="border-left:1px solid #000;
                    border-bottom:1px solid #000;
                    border-right:0;
                    height:18px;
                    padding:3px;">
                <strong>NIVEL:</strong> {{ $estudiante->planDeEstudios->licenciatura->nombreLicenciatura }}
            </td>
        </tr>

        <tr>
            <td style="border-left:1px solid #000;
                    border-bottom:1px solid #000;
                    border-right:0;
                    height:18px;
                    padding:3px;">
                <strong>GENERACIÓN:</strong>
                {{ 
                    $estudiante->generacion->añoDeInicio .
                    (
                        $estudiante->generacion->idMesInicio == 3 ? 'A' :
                        ($estudiante->generacion->idMesInicio == 9 ? 'B' : '')
                    )
                }}
            </td>
        </tr>

        <tr>
            <td style="border-left:1px solid #000;
                    border-bottom:1px solid #000;
                    border-right:0;
                    height:18px;
                    padding:3px;">
                <strong>PERIODO ESCOLAR:</strong> 01
            </td>
        </tr>

    </table>





    <table width="100%" cellspacing="0" cellpadding="0"
        style="border-collapse: collapse; margin-top:20px; table-layout: fixed;">

        <!-- FILA DESCRIPCIÓN -->
        <tr>
            <td width="80%"
                style="border-top:1px solid #000;
                    border-left:1px solid #000;
                    border-bottom:1px solid #000;
                    padding: 3px;        /* padding vertical mayor */
                    height:18px;            /* altura mínima */
                    line-height:1.4;">
                <strong>DESCRIPCIÓN:</strong>
                {{ str_pad($concepto->idConceptoDePago, 2, '0', STR_PAD_LEFT) }}
                {{ $concepto->nombreConceptoDePago }}
            </td>

            <td width="20%"
                style="border-top:1px solid #000;
                    border-left:1px solid #000;   
                    border-right:1px solid #000;
                    border-bottom:1px solid #000;
                    text-align:center;
                    font-weight:bold;
                    height:18px;
                    line-height:1.4;">
                IMPORTE
            </td>
        </tr>

        <!-- FILA APORTACIÓN -->
        <tr>
            <td
                style="border-left:1px solid #000;
                    border-bottom:1px solid #000;
                    padding: 3px;
                    height:18px;
                    line-height:1.4;">
                <strong>APORTACIÓN:</strong>
                {{ $concepto->descripcion ?? 'MES EN CURSO' }}
            </td>

            <td
                style="border-left:1px solid #000;
                    border-right:1px solid #000;
                    border-bottom:1px solid #000;
                    text-align:center;
                    padding: 3px;
                    font-weight:bold;
                    height:18px;
                    line-height:1.4;">
                $ {{ number_format($concepto->costo, 2) }}
            </td>
        </tr>

    </table>




    <table width="100%" cellspacing="0" cellpadding="0"
       style="border-collapse:collapse; margin-top:18px; margin-bottom:12px;">
        <tr>
            <!-- TABLA PRINCIPAL 75% -->
            <td width="75%" style="vertical-align:bottom;">
                <table width="100%" cellspacing="0" cellpadding="0"
                    style="border-collapse: collapse; border:1px solid #000;">
                    <tr>
                        <!-- LOGO -->
                        <td width="15%"
                            style="text-align:center;
                                vertical-align:middle;
                                padding:25px;">
                            <img src="{{ public_path('imagenes/LogoBancoAzteca.png') }}"
                                alt="Logo IDMA"
                                style="width:60px;">
                        </td>

                        <!-- TEXTO CENTRAL -->
                        <td width="35%"
                            style="vertical-align:middle; padding:25px;">
                            <div style="width:100%;
                                        text-align:center;
                                        font-weight:bold;">
                                PAGO DE {{ $concepto->unidad->nombreUnidad ?? 'MES EN CURSO' }}
                            </div>
                        </td>

                        <!-- REFERENCIA -->
                        <td width="50%"
                            style="text-align:center;
                                vertical-align:middle;
                                padding:25px;">
                            <div style="font-weight:bold; margin-bottom:4px;">
                                NÚMERO DE REFERENCIA
                            </div>

                            <div style="font-weight:bold; letter-spacing:1px;">
                                {{ $referencia }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- LÍNEA DEL 25% -->
            <td width="25%" style="vertical-align:bottom;">
                <div style="
                    border-bottom:1px solid #000;
                    height:1px;
                    width:100%;">
                </div>
            </td>
        </tr>
    </table>



    <div class="titulo-copia">
        {{ $i === 0 ? 'COPIA PARA EL ESTUDIANTE' : 'COPIA PARA EL IDMA' }}
    </div>

</div>

@endfor

</body>
</html>
