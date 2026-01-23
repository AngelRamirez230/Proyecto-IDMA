<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de referencias generadas</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @if(session('successPagos'))
        <div class="popup-confirmacion" style="display:flex;">
            <div class="popup-contenido">
                <h3>Pagos generados</h3>

                <p>
                    Pagos creados: <strong>{{ count(session('creados', [])) }}</strong> <br>
                    Pagos existentes: <strong>{{ count(session('duplicados', [])) }}</strong> <br> 
                    Pagos omitidos: <strong>{{ count(session('omitidos', [])) }}</strong>
                </p>

                <button
                    class="popup-boton"
                    onclick="this.closest('.popup-confirmacion').style.display='none'">
                    Ver detalles
                </button>

                <form action="{{ route('admin.pagos.create') }}" method="get">
                    <button type="submit" class="popup-boton">
                        Cerrar
                    </button>
                </form>
            </div>
        </div>
    @endif
    
    @include('layouts.barraNavegacion')


    <main class="consulta">

        <h1 class="titulo-form2">Resumen de generación de pagos</h1>


        <section class="consulta-controles"> 
            <div class="consulta-selects">
                <a href="{{ route('admin.pagos.create') }}"
                    class="btn-boton-formulario2">
                        Volver
                </a>
            </div>
        </section>


        <section class="consulta-tabla-contenedor">


            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Nombre estudiante</th>
                        <th>Referencia</th>
                        <th>Concepto</th>
                        <th>Fecha límite</th>
                        <th>Estatus</th>
                    </tr>
                </thead>

                <tbody class="tabla-cuerpo">

                {{-- REFERENCIAS CREADAS --}}
                @foreach($creados as $pago)
                    <tr>
                        <td>{{ $pago['estudiante'] }}</td>
                        <td>{{ $pago['referencia'] }}</td>
                        <td>{{ $pago['concepto'] }}</td>
                        <td>
                            {{ $pago['fecha'] 
                                ? \Carbon\Carbon::parse($pago['fecha'])->format('d/m/Y') 
                                : '-' }}
                        </td>
                        <td><span class="estatus-activo">Creado</span></td>
                    </tr>
                @endforeach

                {{-- DUPLICADOS --}}
                @foreach($duplicados as $pago)
                    <tr class="fila-suspendida">
                        <td>{{ $pago['estudiante'] }}</td>
                        <td>{{ $pago['referencia'] }}</td>
                        <td>{{ $pago['concepto'] }}</td>
                        <td>
                            {{ $pago['fecha'] 
                                ? \Carbon\Carbon::parse($pago['fecha'])->format('d/m/Y') 
                                : '-' }}
                        </td>
                        <td><span class="estatus-suspendido">Existente</span></td>
                    </tr>
                @endforeach

                @if(empty($creados) && empty($duplicados))
                    <tr>
                        <td colspan="5" class="tablaVacia">
                            No hay información para mostrar.
                        </td>
                    </tr>
                @endif

                </tbody>
            </table>
        </section>


        {{-- ================= OMITIDOS POR PLAN ================= --}}
        @if(!empty($omitidos))
        <section class="consulta-tabla-contenedor" style="margin-top:40px;">

            <h2 class="titulo-form2" style="font-size:22px;">
                Pagos omitidos por plan activo
            </h2>

            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Nombre estudiante</th>
                        <th>Concepto</th>
                        <th>Motivo</th>
                    </tr>
                </thead>

                <tbody class="tabla-cuerpo">

                @foreach($omitidos as $item)
                    <tr class="fila-suspendida">
                        <td>{{ $item['estudiante'] }}</td>
                        <td>{{ $item['concepto'] }}</td>
                        <td>{{ $item['motivo'] }}</td>
                    </tr>
                @endforeach

                @if(empty($omitidos))
                    <tr>
                        <td colspan="3" class="tablaVacia">
                            No hay información para mostrar.
                        </td>
                    </tr>
                @endif

                </tbody>
            </table>

        </section>
        @endif


    </main>

</body>
</html>