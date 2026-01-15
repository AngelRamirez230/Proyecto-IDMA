<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar pago a estudiantes</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="form-container">
        <form method="POST" action="{{ route('admin.pagos.store') }}" class="formulario2">
            @csrf

            <h1 class="titulo-form2">Asignar pago a estudiantes</h1>

            <section class="consulta-controles">

                {{-- BUSCADOR --}}
                <div class="consulta-busqueda-group">
                    <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                    <input
                        type="text"
                        id="buscar"
                        placeholder="Ingresa nombre o matrícula del estudiante"
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

            {{-- CONCEPTO --}}
            <div class="form-group2">
                <label>Concepto de pago:</label>
                <select name="idConceptoDePago" class="select2" required>
                    <option value="" disabled selected>Seleccionar</option>
                    @foreach($conceptos as $concepto)
                        <option value="{{ $concepto->idConceptoDePago }}">
                            {{ $concepto->nombreConceptoDePago }} - ${{ number_format($concepto->costo,2) }}
                        </option>
                    @endforeach
                </select>
                <x-error-field field="idConceptoDePago" />
            </div>

            {{-- FECHA LIMITE --}}
            <div class="form-group2">
                <label>Fecha límite de pago:</label>
                <input
                    type="date"
                    name="fechaLimiteDePago"
                    class="input-chico2"
                    required
                >
                <x-error-field field="fechaLimiteDePago" />
            </div>

            {{-- SELECCIONAR TODOS --}}
            <div class="form-group2" style="margin-bottom:50px;">
                <label class="chk-label">
                    <input type="checkbox" id="selectAll" class="chk-grande">
                    <span>Seleccionar todos los estudiantes</span>
                </label>
            </div>


            <section class="consulta-tabla-contenedor">
                <table class="tabla">

                    <thead>
                        <tr class="tabla-encabezado">
                            <th>Selecionar</th>
                            <th>Nombre del estudiante</th>
                            <th>Matrícula</th>
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

                                <td>
                                    {{ $estudiante->matriculaAlfanumerica }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="tablaVacia">
                                    No hay estudiantes disponibles.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </section>

            <!-- PAGINACIÓN -->
            <div class="paginacion">
                {{ $estudiantes->links() }}
            </div>

            <x-error-field field="estudiantes" />


            {{-- BOTONES --}}
            <div class="form-group2">
                <button type="submit" class="btn-boton-formulario2">
                    Generar pagos
                </button>
                <a href="{{ route('consultaPagos') }}" class="btn-boton-formulario2 btn-cancelar2">
                    Cancelar
                </a>
            </div>
        </form>
    </main>

    {{-- ERRORES --}}
    @if ($errors->any())
        <div style="background:#ffdddd; padding:12px; border:1px solid #cc0000; margin:10px;">
            <strong>Corrige los siguientes errores:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <script>
        document.getElementById('selectAll').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.chk-estudiante');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>


    <script>
        function aplicarFiltros() {
            const params = new URLSearchParams();

            const buscar = document.getElementById('buscar')?.value;
            const filtro = document.getElementById('filtro')?.value;
            const orden  = document.getElementById('orden')?.value;

            if (buscar) params.append('buscar', buscar);
            if (filtro) params.append('filtro', filtro);
            if (orden)  params.append('orden', orden);

            window.location.href = `{{ route('admin.pagos.create') }}?${params.toString()}`;
        }
    </script>
</body>
</html>
