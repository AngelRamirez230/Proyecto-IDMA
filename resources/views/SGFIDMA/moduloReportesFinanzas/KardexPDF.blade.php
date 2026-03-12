<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: A4 portrait;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
        }

        tr {
            page-break-inside: avoid;
        }

        /* ==============================
        TITULO
        ============================== */
        .titulo th {
            background: #A9D18E;
            font-size: 9px;
        }

        /* ==============================
        ENCABEZADOS INFORMACIÓN
        ============================== */
        .encabezado-info th {
            background: #F4B183;
        }

        /* ==============================
        COLUMNAS AZULES
        ============================== */
        .col-azul {
            background: #D9E1F2;
        }

        /* ==============================
        TABLA RESUMEN
        ============================== */
        .tabla-resumen {
            width: 45%;
            margin-top: 25px;
        }

        /* Tabla resumen: alineación personalizada por columna */
        .tabla-resumen td:nth-child(1) {
            text-align: left;   /* Concepto a la izquierda */
            padding-left: 5px;  /* opcional: un pequeño margen */
        }

        .tabla-resumen td:nth-child(2) {
            text-align: center; /* # centrado */
        }

        .tabla-resumen td:nth-child(3) {
            text-align: right;  /* Monto a la derecha */
            padding-right: 5px; /* opcional: un pequeño margen */
        }

        /* Encabezados alineación igual a columnas */
        .tabla-resumen th:nth-child(1) {
            text-align: left;
            padding-left: 5px;
        }
        .tabla-resumen th:nth-child(2) {
            text-align: center;
        }
        .tabla-resumen th:nth-child(3) {
            text-align: right;
            padding-right: 5px;
        }

        td {
            word-wrap: break-word;
        }

        th{
            font-weight: normal;
        }

        /* ==============================
        ENCABEZADO INSTITUCIÓN
        ============================== */

        .header-institucion {
            width: 100%;
            margin-bottom: 10px;
        }

        .header-institucion td {
            border: none;
            vertical-align: middle;
        }

        .logo {
            width: 70px;
        }

        .texto-header {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            line-height: 1.4;
        }

        /* ==============================
        AREA SELLO
        ============================== */

        .contenedor-sello {
            position: relative;
        }

        .area-sello{
            position:absolute;
            right:60px;
            top:0;
            width:220px;
            height:200px;
            border:1px solid #000;
        }

        .area-sello div{
            position:absolute;
            top:50%;
            left:50%;
            transform:translate(-50%, -50%);
            text-align:center;
            color:red;
            font-size:16px;
        }

        .tabla-firmas{
            width:100%;
            margin-top:220px;
        }

        .tabla-firmas td{
            border:none;
            width:50%;
            text-align:center;
            font-size: 14px;
        }

        .firma-container {
            text-align: center;
            vertical-align: top;
        }

        .linea-firma {
            width: 80%;           
            margin: 0 auto;       
            border-top: 1px solid #000;
            margin-bottom: 5px;   
        }

        .nombre-firma {
            max-width: 80%;       
            margin: 0 auto;       
            word-wrap: break-word; 
            overflow-wrap: break-word; 
            line-height: 1.2;     
        }




    </style>

</head>

<body>

    <table class="header-institucion">
        <tr>

            <td style="width:15%; text-align:left;">
                <img src="{{ public_path('imagenes/EscudoIDMA.png') }}" class="logo">
            </td>

            <td class="texto-header" style="width:70%;">
                Sociedad, Mexico Fortalece el Futuro S.C.<br>
                SMF200812IC8<br>
                Instituto Daniel Malpica Altamirano<br>
                Dirección: Transversal 1 S/N, Piso 2, Col. Azteca, C.P. 91183 Xalapa, Ver.
            </td>

            <td style="width:15%; text-align:right;">
                <img src="{{ public_path('imagenes/EscudoIDMA.png') }}" class="logo">
            </td>

        </tr>
    </table>

    <!-- ==============================
         TABLA PRINCIPAL KARDEX
    ============================== -->

    <table>


        <tbody>

            <tr class="titulo">
                <th colspan="7">KARDEX DE PAGOS</th>
            </tr>

            <tr class="encabezado-info">
                <th>Nombre</th>
                <th colspan="6">
                    {{ Str::title(
                        $estudiante->usuario->primerNombre . ' ' .
                        $estudiante->usuario->segundoNombre . ' ' .
                        $estudiante->usuario->primerApellido . ' ' .
                        $estudiante->usuario->segundoApellido
                    ) }}
                </th>
            </tr>

            <tr class="encabezado-info">
                <th>Carrera</th>
                <th colspan="6">
                    {{ $estudiante->planDeEstudios->licenciatura->nombreLicenciatura ?? '-' }}
                </th>
            </tr>

            <tr class="encabezado-info">
                <th><strong>Matrícula</strong></th>
                <th><strong>{{ $estudiante->matriculaAlfanumerica }}</strong></th>
                <th>Generación</th>
                <th colspan="4">{{ $estudiante->generacion->nombreGeneracion }}</th>
            </tr>

            <tr class="encabezado-info">
                <th>Concepto</th>
                <th>Semestre o mes</th>
                <th>Cantidad</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Forma de pago</th>
                <th>Saldo</th>
            </tr>

        

            @php
                $saldo = 0;
            @endphp

            @foreach ($kardex as $fila)

                @php
                    if (($fila['estado'] ?? null) == 11 && !empty($fila['monto'])) {
                        $saldo += $fila['monto'];
                    }
                @endphp

                <tr>

                    <td class="col-azul">
                        @if ($fila['tipo'] === 'semestre')
                            <strong>{{ $fila['concepto'] }}</strong>
                        @else
                            {{ $fila['concepto'] }}
                        @endif
                    </td>

                    <td class="col-azul">
                        @if ($fila['tipo'] === 'semestre')
                            <strong>{{ $fila['periodo'] }}</strong>
                        @else
                            {{ $fila['periodo'] }}
                        @endif
                    </td>

                    <td>
                        @if (($fila['estado'] ?? null) == 11)
                            ${{ number_format($fila['monto'], 2) }}
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        @if (($fila['estado'] ?? null) == 11)
                            ${{ number_format($fila['monto'], 2) }}
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        @if (($fila['estado'] ?? null) == 11 && !empty($fila['fechaPago']))
                            {{ \Carbon\Carbon::parse($fila['fechaPago'])->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        @if (($fila['estado'] ?? null) == 11)
                            {{ $fila['formaPago'] ?? '-' }}
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        @if (($fila['estado'] ?? null) == 11)
                            ${{ number_format($saldo, 2) }}
                        @else
                            -
                        @endif
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>


    <!-- ==============================
         TABLA RESUMEN
    ============================== -->

    <div class="contenedor-sello">

        <table class="tabla-resumen">

            <thead>
                <tr class="titulo">
                    <th>Concepto</th>
                    <th>#</th>
                    <th>Monto</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>{{ $resumen['mensualidad']['concepto'] ?? 'Mensualidad' }}</td>
                    <td>{{ $resumen['mensualidad']['cantidad'] ?? 0 }}</td>
                    <td>{{ isset($resumen['mensualidad']['monto']) ? '$'.number_format($resumen['mensualidad']['monto'], 2) : '-' }}</td>
                </tr>

                <tr>
                    <td>{{ $resumen['inscripcion']['concepto'] ?? 'Inscripción' }}</td>
                    <td>{{ $resumen['inscripcion']['cantidad'] ?? 0 }}</td>
                    <td>{{ isset($resumen['inscripcion']['monto']) ? '$'.number_format($resumen['inscripcion']['monto'], 2) : '-' }}</td>
                </tr>

                <tr>
                    <td>{{ $resumen['recargo']['concepto'] ?? 'Recargo' }}</td>
                    <td>{{ $resumen['recargo']['cantidad'] ?? 0 }}</td>
                    <td>{{ isset($resumen['recargo']['monto']) ? '$'.number_format($resumen['recargo']['monto'], 2) : '-' }}</td>
                </tr>

                <tr>
                    <td>{{ $resumen['examen']['concepto'] ?? 'Examen' }}</td>
                    <td>{{ $resumen['examen']['cantidad'] ?? 0 }}</td>
                    <td>{{ isset($resumen['examen']['monto']) ? '$'.number_format($resumen['examen']['monto'], 2) : '-' }}</td>
                </tr>

                <tr>
                    <td>{{ $resumen['uniforme']['concepto'] ?? 'Uniforme' }}</td>
                    <td>{{ $resumen['uniforme']['cantidad'] ?? 0 }}</td>
                    <td>{{ isset($resumen['uniforme']['monto']) ? '$'.number_format($resumen['uniforme']['monto'], 2) : '-' }}</td>
                </tr>

                <tr>
                    <td>Total pagado</td>
                    <td>{{ $totalCantidad ?? 0 }}</td>
                    <td>{{ isset($totalPagado) ? '$'.number_format($totalPagado, 2) : '-' }}</td>
                </tr>
            </tbody>

        </table>

        <div class="area-sello">
            <div>
                SELLO<br>
                DE LA<br>
                INSTITUCIÓN
            </div>
        </div>

    </div>


    <table class="tabla-firmas">
        <tr>
            <td class="firma-container">
                <div class="linea-firma"></div>
                <div class="nombre-firma">
                    Dra. María Alejandrinan Uscanga Castillo
                </div>
                <div>
                    Directora de Carrera
                </div>
            </td>

            <td class="firma-container">
                <div class="linea-firma"></div>
                <div class="nombre-firma">
                    {{ 
                        ucwords(strtolower(
                            $estudiante->usuario->primerNombre . ' ' .
                            $estudiante->usuario->segundoNombre . ' ' .
                            $estudiante->usuario->primerApellido . ' ' .
                            $estudiante->usuario->segundoApellido
                        )) 
                    }}
                </div>
                <div>
                    Alumno(a)
                </div>
            </td>
        </tr>
    </table>

</body>

</html>