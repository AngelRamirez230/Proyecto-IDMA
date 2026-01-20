<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar grupo</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    @include('layouts.barraNavegacion')

    <main class="form-container">
        <form action="{{ route('grupos.update', $grupo->idGrupo) }}" method="POST" class="formulario">
            @csrf
            @method('PUT')

            <h1 class="titulo-form">Editar grupo</h1>

            <div class="form-group">
                <label for="idCicloEscolar">Ciclo escolar:</label>
                <select id="idCicloEscolar" name="idCicloEscolar" class="select" required>
                    <option value="" disabled {{ old('idCicloEscolar', $grupo->idCicloEscolar ?? '') ? '' : 'selected' }}>
                        Seleccionar
                    </option>
                    @foreach($ciclos as $ciclo)
                        <option
                            value="{{ $ciclo->idCicloEscolar }}"
                            {{ old('idCicloEscolar', $grupo->idCicloEscolar ?? '') == $ciclo->idCicloEscolar ? 'selected' : '' }}
                        >
                            {{ $ciclo->nombreCicloEscolar }}
                        </option>
                    @endforeach
                </select>
                <x-error-field field="idCicloEscolar" />
            </div>

            <div class="form-group">
                <label for="idCicloModalidad">Modalidad:</label>
                <select id="idCicloModalidad" name="idCicloModalidad" class="select" required disabled>
                    <option value="" disabled selected>
                        Seleccionar
                    </option>
                </select>
                <x-error-field field="idCicloModalidad" />
            </div>

            <div class="form-group">
                <label for="idLicenciatura">Carrera:</label>
                <select id="idLicenciatura" name="idLicenciatura" class="select" required>
                    <option value="" disabled {{ old('idLicenciatura', $grupo->idLicenciatura ?? '') ? '' : 'selected' }}>
                        Seleccionar
                    </option>
                    @foreach($licenciaturas as $licenciatura)
                        <option
                            value="{{ $licenciatura->idLicenciatura }}"
                            {{ old('idLicenciatura', $grupo->idLicenciatura ?? '') == $licenciatura->idLicenciatura ? 'selected' : '' }}
                        >
                            {{ $licenciatura->nombreLicenciatura }}
                        </option>
                    @endforeach
                </select>
                <x-error-field field="idLicenciatura" />
            </div>

            <div class="form-group">
                <label for="semestre">Semestre:</label>
                <input
                    type="number"
                    id="semestre"
                    name="semestre"
                    class="input-chico"
                    min="1"
                    max="12"
                    step="1"
                    value="{{ old('semestre', $grupo->semestre ?? '') }}"
                    required
                >
                <x-error-field field="semestre" />
            </div>

            <div class="form-group">
                <label for="claveGrupo">Clave del grupo:</label>
                <input
                    type="text"
                    id="claveGrupo"
                    name="claveGrupo"
                    class="input-mediano"
                    placeholder="Si lo dejas en blanco, se genera automaticamente"
                    value="{{ old('claveGrupo', $grupo->claveGrupo ?? '') }}"
                >
                <x-error-field field="claveGrupo" />
            </div>

            <div class="form-group">
                <button type="submit" class="btn-boton-formulario">Guardar</button>
                <a href="{{ route('consultaGrupo') }}" class="btn-boton-formulario btn-cancelar">
                    Cancelar
                </a>
            </div>
        </form>
    </main>

    <section class="consulta-tabla-contenedor">
        <h2 class="consulta-titulo consulta-subtitulo">Estudiantes del grupo</h2>

        <form action="{{ route('grupos.desasignarEstudiantes', $grupo->idGrupo) }}" method="POST" id="formDesasignarEstudiantes">
            @csrf

            <div class="acciones-encabezado acciones-encabezado--tabla">
                <button type="button" class="btn-boton-formulario2 btn-boton-formulario2--auto" onclick="abrirPopupAsignacion()">
                    Asignar estudiantes
                </button>
                <button type="submit" class="btn-boton-formulario2-1 btn-boton-formulario2--auto" id="btnDesasignar" disabled>
                    Des-asignar estudiantes
                </button>
            </div>

            <table class="tabla">
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Seleccionar</th>
                        <th>Matrícula</th>
                        <th>Nombre completo</th>
                        <th>Generación</th>
                    </tr>
                </thead>
                <tbody class="tabla-cuerpo">
                    @if ($estudiantes->isEmpty())
                        <tr>
                            <td colspan="4" class="tablaVacia">
                                No hay estudiantes inscritos a este grupo.
                            </td>
                        </tr>
                    @else
                        @foreach ($estudiantes as $estudiante)
                            @php
                                $nombreCompleto = trim(
                                    ($estudiante->primerNombre ?? '') . ' ' .
                                    ($estudiante->segundoNombre ?? '') . ' ' .
                                    ($estudiante->primerApellido ?? '') . ' ' .
                                    ($estudiante->segundoApellido ?? '')
                                );
                            @endphp
                            <tr class="tabla-fila">
                                <td>
                                    <input type="checkbox"
                                        name="estudiantes[]"
                                        value="{{ $estudiante->idEstudiante ?? '' }}"
                                        onchange="actualizarSeleccionDesasignar()">
                                </td>
                                <td>{{ $estudiante->matriculaAlfanumerica ?? 'Sin matricula' }}</td>
                                <td>{{ $nombreCompleto ?: 'Sin nombre' }}</td>
                                <td>{{ $estudiante->idGeneracion ?? 'N/D' }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </form>
    </section>

    <div class="popup-confirmacion" id="popupAsignacion">
        <div class="popup-contenido popup-contenido--ancho">
            <h3>Asignar estudiantes</h3>

            <div class="acciones-encabezado">
                <div id="contadorSeleccionPopup" class="contador-seleccion">Seleccionados: 0</div>
            </div>

            <form action="{{ route('grupos.asignarEstudiantes', $grupo->idGrupo) }}" method="POST" id="formAsignarEstudiantes">
                @csrf

                <div class="consulta-busqueda-group busqueda-popup">
                    <img src="{{ asset('imagenes/IconoBusqueda.png') }}" alt="Buscar">
                    <input
                        type="text"
                        id="buscarEstudianteAsignacion"
                        placeholder="Buscar por matricula, generacion o nombre"
                        oninput="filtrarEstudiantesAsignacion()"
                    >
                </div>

                <div class="popup-tabla-wrap">
                    <table class="tabla">
                        <thead>
                            <tr class="tabla-encabezado">
                                <th>
                                    <input type="checkbox" id="seleccionarTodo" onclick="toggleSeleccionTodos(this)">
                                </th>
                                <th>Matrícula</th>
                                <th>Nombre completo</th>
                                <th>Generación</th>
                                <th>Semestre</th>
                            </tr>
                        </thead>
                        <tbody class="tabla-cuerpo" id="tablaAsignacionBody">
                            @if ($disponibles->isEmpty())
                                <tr>
                                    <td colspan="5" class="tablaVacia">
                                        No hay estudiantes disponibles para asignar.
                                    </td>
                                </tr>
                            @else
                                @foreach ($disponibles as $estudiante)
                                    @php
                                        $nombreCompleto = trim(
                                            ($estudiante->primerNombre ?? '') . ' ' .
                                            ($estudiante->segundoNombre ?? '') . ' ' .
                                            ($estudiante->primerApellido ?? '') . ' ' .
                                            ($estudiante->segundoApellido ?? '')
                                        );
                                    @endphp
                                    <tr class="tabla-fila estudiante-fila">
                                        <td>
                                            <input type="checkbox" name="estudiantes[]" value="{{ $estudiante->idEstudiante }}" onchange="actualizarContadorSeleccion()">
                                        </td>
                                        <td class="col-matricula">{{ $estudiante->matriculaAlfanumerica ?? 'Sin matrícula' }}</td>
                                        <td class="col-nombre">{{ $nombreCompleto ?: 'Sin nombre' }}</td>
                                        <td class="col-generacion">{{ $estudiante->idGeneracion ?? 'N/D' }}</td>
                                        <td>{{ $estudiante->grado ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="popup-botones popup-botones--separado">
                    <button type="submit" class="btn-confirmar">Asignar</button>
                    <button type="button" class="btn-cancelar-confirmacion" onclick="cerrarPopupAsignacion()">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <div class="bloque-errores">
            <strong>Corrige los siguientes errores:</strong>
            <ul class="bloque-errores-lista">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cicloSelect = document.getElementById('idCicloEscolar');
            const modalidadSelect = document.getElementById('idCicloModalidad');

            const modalidades = @json($cicloModalidades);
            const oldModalidad = "{{ old('idCicloModalidad', $grupo->idCicloModalidad ?? '') }}";
            const oldCiclo = "{{ old('idCicloEscolar', $grupo->idCicloEscolar ?? '') }}";

            const resetModalidades = (placeholder, disabled = true) => {
                modalidadSelect.innerHTML = `<option value="">${placeholder}</option>`;
                modalidadSelect.disabled = disabled;
            };

            const cargarModalidades = (idCiclo) => {
                const opciones = modalidades.filter(m => String(m.idCicloEscolar) === String(idCiclo));
                resetModalidades('Seleccionar', opciones.length === 0);

                opciones.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.idCicloModalidad;
                    option.textContent = item.nombreModalidad;
                    if (oldModalidad && String(oldModalidad) === String(item.idCicloModalidad)) {
                        option.selected = true;
                    }
                    modalidadSelect.appendChild(option);
                });
            };

            if (oldCiclo) {
                cicloSelect.value = oldCiclo;
                cargarModalidades(oldCiclo);
            } else {
                resetModalidades('Seleccionar', true);
            }

            cicloSelect.addEventListener('change', function () {
                if (!this.value) {
                    resetModalidades('Seleccionar', true);
                    return;
                }
                cargarModalidades(this.value);
            });
        });
    </script>

    <script>
        function abrirPopupAsignacion() {
            const popup = document.getElementById('popupAsignacion');
            if (popup) {
                popup.style.display = 'flex';
            }
            const seleccionarTodo = document.getElementById('seleccionarTodo');
            if (seleccionarTodo) {
                seleccionarTodo.checked = false;
            }
            actualizarContadorSeleccion();
        }

        function cerrarPopupAsignacion() {
            const popup = document.getElementById('popupAsignacion');
            if (popup) {
                popup.style.display = 'none';
            }
        }

        function filtrarEstudiantesAsignacion() {
            const input = document.getElementById('buscarEstudianteAsignacion');
            const term = input ? input.value.toLowerCase().trim() : '';
            const filas = document.querySelectorAll('#tablaAsignacionBody .estudiante-fila');

            if (!filas.length) {
                return;
            }

            let visibles = 0;
            filas.forEach(fila => {
                const matricula = fila.querySelector('.col-matricula')?.textContent?.toLowerCase() || '';
                const nombre = fila.querySelector('.col-nombre')?.textContent?.toLowerCase() || '';
                const generacion = fila.querySelector('.col-generacion')?.textContent?.toLowerCase() || '';

                const coincide = matricula.includes(term) || nombre.includes(term) || generacion.includes(term);
                fila.style.display = coincide ? '' : 'none';
                if (coincide) visibles += 1;
            });

            const vacia = document.getElementById('filaSinCoincidencias');
            if (vacia) {
                vacia.remove();
            }

            if (visibles === 0) {
                const tbody = document.getElementById('tablaAsignacionBody');
                if (tbody) {
                    const tr = document.createElement('tr');
                    tr.id = 'filaSinCoincidencias';
                    tr.innerHTML = '<td colspan="5" class="tablaVacia">No se encontraron coincidencias.</td>';
                    tbody.appendChild(tr);
                }
            }
        }

        function actualizarContadorSeleccion() {
            const checks = document.querySelectorAll('#tablaAsignacionBody input[type="checkbox"][name="estudiantes[]"]');
            let total = 0;
            checks.forEach(chk => {
                if (chk.checked && chk.closest('.estudiante-fila')?.style.display !== 'none') {
                    total += 1;
                }
            });
            const label = document.getElementById('contadorSeleccionPopup');
            if (label) {
                label.textContent = `Seleccionados: ${total}`;
            }
        }

        function toggleSeleccionTodos(checkbox) {
            const checks = document.querySelectorAll('#tablaAsignacionBody input[type="checkbox"][name="estudiantes[]"]');
            checks.forEach(chk => {
                if (chk.closest('.estudiante-fila')?.style.display === 'none') {
                    return;
                }
                chk.checked = checkbox.checked;
            });
            actualizarContadorSeleccion();
        }
    </script>

    <script>
        function actualizarSeleccionDesasignar() {
            const form = document.getElementById('formDesasignarEstudiantes');
            const checks = form
                ? form.querySelectorAll('input[type="checkbox"][name="estudiantes[]"]')
                : [];
            let total = 0;
            checks.forEach(chk => {
                if (chk.checked) {
                    total += 1;
                }
            });

            const boton = document.getElementById('btnDesasignar');
            if (boton) {
                boton.disabled = total === 0;
            }
        }

        const formDesasignar = document.getElementById('formDesasignarEstudiantes');
        if (formDesasignar) {
            formDesasignar.querySelectorAll('input[type="checkbox"][name="estudiantes[]"]').forEach(chk => {
                chk.addEventListener('change', actualizarSeleccionDesasignar);
            });
        }

        const formDesasignarListener = document.getElementById('formDesasignarEstudiantes');
        if (formDesasignarListener) {
            formDesasignarListener.addEventListener('submit', function (e) {
                const checks = formDesasignarListener.querySelectorAll('input[type="checkbox"][name="estudiantes[]"]');
                const haySeleccion = Array.from(checks).some(chk => chk.checked);
                if (!haySeleccion) {
                    e.preventDefault();
                    return;
                }

                const popup = document.getElementById('popupConfirmacion');
                const msg   = document.getElementById('mensajeConfirmacion');

                if (!popup || !msg) {
                    return;
                }

                e.preventDefault();
                formularioAEliminar = formDesasignarListener;
                msg.textContent = 'Deseas des-asignar a los estudiantes seleccionados?';
                popup.style.display = 'flex';
            });
        }

        actualizarSeleccionDesasignar();
    </script>
</body>
</html>
