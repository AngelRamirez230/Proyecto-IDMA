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
                <div class="consulta-busqueda-group barra-busqueda-asignacion-de-pago">
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


            <div class="bloque-horizontal-pagos">

                {{-- CONCEPTO --}}
                <div class="form-group">
                    <label>Concepto de pago:</label>
                    <select name="idConceptoDePago" class="select" required>
                        <option value="" disabled {{ old('idConceptoDePago') ? '' : 'selected' }}>
                            Seleccionar
                        </option>
                        @foreach($conceptos as $concepto)
                            <option value="{{ $concepto->idConceptoDePago }}"
                                {{ old('idConceptoDePago') == $concepto->idConceptoDePago ? 'selected' : '' }}>
                                {{ $concepto->nombreConceptoDePago }} - ${{ number_format($concepto->costo,2) }}
                            </option>
                        @endforeach
                    </select>
                    <x-error-field field="idConceptoDePago" />
                </div>


                {{-- CICLO --}}
                <div class="form-group">
                    <label>Ciclo escolar:</label>
                    <select name="idCicloModalidad" id="selectCiclo" class="select" required>
                        <option value="" disabled {{ old('idCicloModalidad') ? '' : 'selected' }}>
                            Seleccionar
                        </option>

                        @foreach($ciclos as $ciclo)
                            <option value="{{ $ciclo->idCicloModalidad }}"
                                {{ old('idCicloModalidad') == $ciclo->idCicloModalidad ? 'selected' : '' }}>
                                
                                {{ $ciclo->cicloEscolar->nombreCicloEscolar }}
                                - 
                                {{ $ciclo->modalidad->nombreModalidad }}

                            </option>
                        @endforeach
                    </select>

                    <x-error-field field="idCicloModalidad" />
                </div>



                {{-- FECHA DE EMISIÓN--}}
                <div class="form-group">
                    <label>Fecha de emisión de pago:</label>
                    <input
                        type="date"
                        name="fechaEmisionDePago"
                        class="input-chico"
                        value="{{ old('fechaEmisionDePago') }}"
                        min="{{ now()->toDateString() }}"
                        required
                    >
                    <x-error-field field="fechaLimiteDePago" />
                </div>



                {{-- FECHA LIMITE --}}
                <div class="form-group">
                    <label>Fecha límite de pago:</label>
                    <input
                        type="date"
                        name="fechaLimiteDePago"
                        class="input-chico"
                        value="{{ old('fechaLimiteDePago') }}"
                        min="{{ now()->toDateString() }}"
                        required
                    >
                    <x-error-field field="fechaLimiteDePago" />
                </div>


                {{-- APORTACIÓN --}}
                <div class="form-group">
                    <label>Aportación:</label>
                    <input
                        type="text"
                        name="aportacion"
                        class="input-chico"
                        placeholder="Ingresa aportación"
                        value="{{ old('aportacion') }}"
                        required
                    >
                    <x-error-field field="aportacion" />
                </div>


                {{-- DESCUENTO DE PAGO --}}
                <div class="form-group">
                    <label>Descuento de pago:</label>
                    <input
                        type="number"
                        name="descuentoDePago"
                        class="input-chico"
                        placeholder="Ingresa descuento"
                        value="{{ old('descuentoDePago') }}"
                    >
                    <x-error-field field="descuentoDePago" />
                </div>

            </div>



            <section class="consulta-tabla-contenedor">
                <table class="tabla">

                    <thead>
                        <tr class="tabla-encabezado">
                            <th style="text-align:center; white-space:nowrap;">
                                <label style="display:flex; align-items:center; justify-content:center; gap:6px; cursor:pointer;">
                                    <input type="checkbox" id="selectAll" class="chk-grande">
                                    <span>Seleccionar</span>
                                </label>
                            </th>
                            <th>Nombre del estudiante</th>
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

                                <td>
                                    {{ $estudiante->matriculaAlfanumerica }}
                                </td>
                                <td>
                                    {{ $estudiante->grado }}
                                </td>
                                <td>
                                    {{ $estudiante->planDeEstudios->licenciatura->nombreLicenciatura }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="tablaVacia">
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

    </main>

    <script>
        document.getElementById('selectAll').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.chk-estudiante');
            checkboxes.forEach(cb => cb.checked = this.checked);
            actualizarCiclos(); 
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


    <script>
            document.addEventListener("DOMContentLoaded", function() {

                document.querySelectorAll('.chk-estudiante').forEach(checkbox => {
                    checkbox.addEventListener('change', actualizarCiclos);
                });

            });

            function actualizarCiclos() {

                const seleccionados = Array.from(
                    document.querySelectorAll('.chk-estudiante:checked')
                ).map(cb => cb.value);

                fetch("{{ route('admin.pagos.ciclosPorEstudiantes') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        estudiantes: seleccionados
                    })
                })
                .then(response => response.json())
                .then(data => {

                    const select = document.getElementById('selectCiclo');
                    select.innerHTML = '';

                    if (data.length === 0) {
                        const option = document.createElement('option');
                        option.text = 'No hay ciclos coincidentes';
                        option.disabled = true;
                        option.selected = true;
                        select.appendChild(option);
                        return;
                    }

                    const defaultOption = document.createElement('option');
                    defaultOption.text = 'Seleccionar';
                    defaultOption.disabled = true;
                    defaultOption.selected = true;
                    select.appendChild(defaultOption);

                    data.forEach(ciclo => {
                        const option = document.createElement('option');
                        option.value = ciclo.idCicloModalidad;
                        option.text =
                            ciclo.ciclo_escolar.nombreCicloEscolar +
                            " - " +
                            ciclo.modalidad.nombreModalidad;
                        select.appendChild(option);
                    });

                });
            }
        </script>



</body>
</html>
