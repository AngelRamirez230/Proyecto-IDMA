<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de plan de pagos</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @include('layouts.barraNavegacion')

    <form method="POST" action="{{ route('admin.planPago.asignar.store') }}" class="formulario2">
        @csrf

        <h1 class="titulo-form2">Asignar plan de pago a estudiantes</h1>

        {{-- CONTROLES --}}
        <section class="consulta-controles">

            {{-- BUSCADOR --}}
            <div class="consulta-busqueda-group barra-busqueda-asignacion-de-pago">
                <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                <input
                    type="text"
                    id="buscar"
                    placeholder="Nombre o matrícula del estudiante"
                    value="{{ request('buscar') }}"
                    onkeydown="if(event.key === 'Enter'){ aplicarFiltros(); }"
                />
            </div>

            {{-- FILTROS Y ORDEN --}}
            <div class="consulta-selects">

                <select id="filtro" class="select select-boton" onchange="aplicarFiltros()">
                    <option value="">Filtrar por</option>
                    <option value="nuevoIngreso" {{ request('filtro') == 'nuevoIngreso' ? 'selected' : '' }}>
                        Nuevo ingreso
                    </option>
                    <option value="inscritos" {{ request('filtro') == 'inscritos' ? 'selected' : '' }}>
                        Inscritos
                    </option>
                </select>

                <select id="orden" class="select select-boton" onchange="aplicarFiltros()">
                    <option value="">Ordenar por</option>
                    <option value="alfabetico" {{ request('orden') == 'alfabetico' ? 'selected' : '' }}>
                        Alfabéticamente (A–Z)
                    </option>
                </select>

            </div>
        </section>

        {{-- PLAN DE PAGO --}}
        <div class="form-group2">
            <label>Plan de pago:</label>
            <select name="idPlanDePago" class="select2" required>
                <option value="" disabled selected>Seleccionar</option>
                @foreach($planes as $plan)
                    <option value="{{ $plan->idPlanDePago }}">
                        {{ $plan->nombrePlanDePago }}
                    </option>
                @endforeach
            </select>
            <x-error-field field="idPlanDePago" />
        </div>

        {{-- FECHA DE FINALIZACIÓN --}}
        <div class="form-group2">
            <label>Fecha de finalización del plan:</label>
            <input
                type="date"
                name="fechaDeFinalizacion"
                class="select2"
                min="{{ now()->toDateString() }}"
                required
            >
            <x-error-field field="fechaDeFinalizacion" />
        </div>

        {{-- SELECCIONAR TODOS --}}
        <div class="form-group2" style="margin-bottom:40px;">
            <label class="chk-label">
                <input type="checkbox" id="selectAll" class="chk-grande">
                <span>Seleccionar todos los estudiantes</span>
            </label>
        </div>

        {{-- TABLA --}}
        <section class="consulta-tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Seleccionar</th>
                        <th>Nombre</th>
                        <th>Matrícula</th>
                        <th>Semestre</th>
                        <th>Licenciatura</th>
                    </tr>
                </thead>

                <tbody class="tabla-cuerpo">
                    @forelse($estudiantes as $estudiante)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    name="estudiantes[]"
                                    value="{{ $estudiante->idEstudiante }}"
                                    class="chk-estudiante chk-grande"
                                >
                            </td>

                            <td>
                                {{ $estudiante->usuario->primerNombre }}
                                {{ $estudiante->usuario->segundoNombre }}
                                {{ $estudiante->usuario->primerApellido }}
                                {{ $estudiante->usuario->segundoApellido }}
                            </td>

                            <td>{{ $estudiante->matriculaAlfanumerica }}</td>
                            <td>{{ $estudiante->grado }}</td>
                            <td>{{ $estudiante->planDeEstudios->licenciatura->nombreLicenciatura }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="tablaVacia">
                                No hay estudiantes disponibles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        {{-- PAGINACIÓN --}}
        <div class="paginacion">
            {{ $estudiantes->appends(request()->query())->links() }}
        </div>

        <x-error-field field="estudiantes" />

        {{-- BOTONES --}}
        <div class="form-group2">
            <button type="submit" class="btn-boton-formulario2">
                Asignar plan
            </button>
            <a href="{{ route('consultaPlan') }}" class="btn-boton-formulario2 btn-cancelar2">
                Cancelar
            </a>
        </div>


        {{-- BLOQUE DE ERRORES DE VALIDACIÓN --}}
        @if ($errors->any())
            <div style="background:#ffdddd; padding:12px; border:1px solid #cc0000; margin:10px 0;">
                <strong>Corrige los siguientes errores:</strong>
                <ul style="margin: 8px 0 0 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </form>

    {{-- JS --}}
    <script>
        document.getElementById('selectAll').addEventListener('change', function () {
            document.querySelectorAll('.chk-estudiante')
                .forEach(cb => cb.checked = this.checked);
        });

        function aplicarFiltros() {
            const params = new URLSearchParams();

            const buscar = document.getElementById('buscar')?.value;
            const filtro = document.getElementById('filtro')?.value;
            const orden  = document.getElementById('orden')?.value;

            if (buscar) params.append('buscar', buscar);
            if (filtro) params.append('filtro', filtro);
            if (orden)  params.append('orden', orden);

            window.location.href =
                `{{ route('admin.planPago.asignar.create') }}?${params.toString()}`;
        }
    </script>

</body>
</html>
