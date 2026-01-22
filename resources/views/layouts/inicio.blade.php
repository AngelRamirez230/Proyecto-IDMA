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

    @foreach($notificaciones as $notificacion)
        <div class="popup-confirmacion" style="display:flex;">
            <div class="popup-contenido">
                <h3>{{ $notificacion->titulo }}</h3>
                <p>
                    <strong>Para:</strong> 
                    {{ $notificacion->usuario->primerNombre ?? '' }} 
                    {{ $notificacion->usuario->segundoNombre ?? '' }} 
                    {{ $notificacion->usuario->primerApellido ?? '' }} 
                    {{ $notificacion->usuario->segundoApellido ?? '' }}
                    <br>
                    {{ $notificacion->mensaje }}
                    
                </p>
                <button class="popup-boton" onclick="marcarComoLeida({{ $notificacion->idNotificacion }})">
                    Entendido
                </button>
            </div>
        </div>
    @endforeach


    @if(isset($datosGeneracion) && $datosGeneracion)
        <div id="popupGeneracion" class="popup-confirmacion" style="display:flex;">
            <div class="popup-contenido">
                <h3>Nueva generacion detectada</h3>

                <p>
                    Se sugiere crear la generacion:
                    <strong>{{ $datosGeneracion['nombreGeneracion'] }}</strong>
                </p>

                <form action="{{ route('generaciones.crearDashboard') }}" method="POST">
                    @csrf

                    @foreach($datosGeneracion as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="popup-botones">
                        <button type="submit" class="btn-confirmar">
                            Crear generacion
                        </button>

                        <button type="button"
                                class="btn-cancelar-confirmacion"
                                onclick="cerrarPopupGeneracion()">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

<!-- CONTENIDO -->
<div class="content">
    <div class="btn-grid">

        {{-- 1. USUARIOS --}}
        @admin
        <a href="{{ route('apartadoUsuarios') }}" class="card-btn">
            <img src="/imagenes/IconoInicioUsuarios.png" alt="">
            <span class="card-btn-title">Usuarios</span>
        </a>
        @endadmin

        {{-- 2. ESTUDIANTES --}}
        @if(Auth::user()->esAdmin())
        <a href="{{ route('apartadoEstudiantes') }}" class="card-btn">
            <img src="/imagenes/IconoInicioEstudiantes.png" alt="">
            <span class="card-btn-title">Estudiantes</span>
        </a>
        @endif

        {{-- 3. DOCENTES --}}
        @admin
        <a href="#" class="card-btn">
            <img src="/imagenes/IconoInicioDocentes.png" alt="">
            <span class="card-btn-title">Docentes</span>
        </a>
        @endadmin

        {{-- 4. ASIGNATURAS --}}
        @admin
        <a href="{{ route('apartadoAsignaturas') }}" class="card-btn">
            <img src="/imagenes/IconoInicioAsignaturas.png" alt="">
            <span class="card-btn-title">Asignaturas</span>
        </a>
        @endadmin

        {{-- 5. GRUPOS --}}
        @php
            $puedeVerGrupos = auth()->check()
                && in_array((int) auth()->user()->idtipoDeUsuario, [1, 2], true);
        @endphp
        @if($puedeVerGrupos)
            <a href="{{ route('apartadoGrupos') }}" class="card-btn">
                <img src="/imagenes/IconoInicioGrupos.png" alt="">
                <span class="card-btn-title">Grupos</span>
            </a>
        @endif

        {{-- 6. HORARIOS --}}
        @admin
        <a href="#" class="card-btn">
            <img src="/imagenes/IconoInicioHorarios.png" alt="">
            <span class="card-btn-title">Horarios</span>
        </a>
        @endadmin

        {{-- 7. CALIFICACIONES --}}
        @admin
        <a href="#" class="card-btn">
            <img src="/imagenes/IconoInicioCalificaciones.png" alt="">
            <span class="card-btn-title">Calificaciones</span>
        </a>
        @endadmin

        {{-- 8. BECAS --}}
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11) || Auth::user()->estudiante)
        <a href="{{ route('apartadoBecas') }}" class="card-btn">
            <img src="/imagenes/IconoInicioBecas.png" alt="">
            <span class="card-btn-title">Becas</span>
        </a>
        @endif

        {{-- 9. PAGOS --}}
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11) || Auth::user()->estudiante)
        <a href="{{ route('apartadoPagos') }}" class="card-btn">
            <img src="/imagenes/IconoInicioPagos.png" alt="">
            <span class="card-btn-title">Pagos</span>
        </a>
        @endif

        {{-- 10. CONCEPTOS DE PAGO --}}
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11) || Auth::user()->estudiante)
        <a href="{{ route('apartadoConceptos') }}" class="card-btn">
            <img src="/imagenes/IconoConceptodepago.png" alt="">
            <span class="card-btn-title">Conceptos de pago</span>
        </a>
        @endif

        {{-- 11. PLANES DE PAGO --}}
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11))
        <a href="{{ route('apartadoPlanDePago') }}" class="card-btn">
            <img src="/imagenes/IconoInicioPlanesdepago.png" alt="">
            <span class="card-btn-title">Planes de pago</span>
        </a>
        @endif

        {{-- 12. REPORTES --}}
        @if(Auth::user()->esAdmin() || Auth::user()->esEmpleadoDe(11))
        <a href="{{ route('apartadoReportes') }}" class="card-btn">
            <img src="/imagenes/IconoInicioReportes.png" alt="">
            <span class="card-btn-title">Reportes</span>
        </a>
        @endif

        {{-- 13. BITÁCORAS --}}
        @admin
        <a href="{{ route('apartadoBitacoras') }}" class="card-btn">
            <img src="/imagenes/IconoBitacorasdelsistema.png" alt="">
            <span class="card-btn-title">Bítacoras del sistema</span>
        </a>
        @endadmin

    </div>
</div>



    @php
        $pagosOrdenados = collect($pagos);

        // Inscripción o reinscripción (solo uno de los dos existirá)
        $principal = $pagosOrdenados->first(fn ($p) =>
            in_array($p->idConceptoDePago, [1, 30])
        );

        // Mensualidades ordenadas por fecha límite
        $mensualidades = $pagosOrdenados
            ->where('idConceptoDePago', 2)
            ->sortBy('fechaLimiteDePago');

        // Unir todo
        $pagosOrdenadosFinal = collect();

        if ($principal) {
            $pagosOrdenadosFinal->push($principal);
        }

        $pagosOrdenadosFinal = $pagosOrdenadosFinal->merge($mensualidades);
    @endphp


    @if($pagos->count())

        <section class="consulta">

            <h2 class="consulta-titulo titulo-centrado">
                {{ $planAsignado->planDePago->nombrePlanDePago }}
            </h2>

            <section class="consulta-tabla-contenedor">
                <table class="tabla">

                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Aportación</th>
                            <th>Fecha limite de pago</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($pagosOrdenadosFinal as $pago)

                            @php
                                $yaGenerado = now()->gte($pago->fechaGeneracionDePago);
                            @endphp

                            <tr>
                                <td>{{ $pago->concepto->nombreConceptoDePago }}</td>

                                <td>{{ $pago->aportacion }}</td>

                                <td>{{ $pago->fechaLimiteDePago->format('d/m/Y') }}</td>

                                <td>
                                    <span class="estatus estatus-{{ strtolower($pago->estatus->nombreTipoDeEstatus) }}">
                                        {{ $pago->estatus->nombreTipoDeEstatus }}
                                    </span>
                                </td>

                                <td>
                                    <div class="tabla-acciones">

                                        @php
                                            $yaGenerado = now()->gte($pago->fechaGeneracionDePago);
                                        @endphp

                                        {{-- VER DETALLES --}}
                                        <a href="{{ $yaGenerado ? route('pagos.show', $pago->Referencia) : '#' }}"
                                        class="btn-boton-formulario2 btn-accion {{ $yaGenerado ? '' : 'btn-desabilitado' }}"
                                        title="{{ $yaGenerado ? 'Ver detalles' : 'Disponible próximamente' }}">
                                            Ver detalles
                                        </a>

                                        {{-- DESCARGAR RECIBO --}}
                                        <a href="{{ ($yaGenerado) ? route('pagos.recibo', $pago->Referencia) : '#' }}"
                                        class="btn-boton-formulario2 btn-accion {{ ($yaGenerado) ? '' : 'btn-desabilitado' }}"
                                        title="{{ ($yaGenerado) ? 'Descargar recibo' : 'No disponible aún' }}">
                                            Descargar recibo
                                        </a>

                                    </div>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>
            </section>

        </section>

    @endif





    <script>
    function marcarComoLeida(id) {
        fetch(`/notificaciones/${id}/leida`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        }).then(() => location.reload());
    }
    </script>
</body>
</html>
