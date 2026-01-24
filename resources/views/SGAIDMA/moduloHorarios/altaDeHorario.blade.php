<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de horarios</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    @include('layouts.barraNavegacion')

    <main class="form-container">
        <form action="{{ route('horarios.store') }}" method="POST" class="formulario" id="formHorario">
            @csrf

            <h1 class="titulo-form">Alta de horario</h1>

            <div class="form-group">
                <label for="idGrupo">Grupo:</label>
                <select id="idGrupo" name="idGrupo" class="select" required>
                    <option value="" disabled {{ old('idGrupo') ? '' : 'selected' }}>Seleccionar</option>
                    @foreach($grupos as $grupo)
                        <option value="{{ $grupo->idGrupo }}" {{ old('idGrupo') == $grupo->idGrupo ? 'selected' : '' }}>
                            {{ $grupo->claveGrupo }}
                        </option>
                    @endforeach
                </select>
                <x-error-field field="idGrupo" />
            </div>

            <div class="form-group">
                <label for="idCicloEscolar">Ciclo escolar:</label>
                <select id="idCicloEscolar" name="idCicloEscolar" class="select" required>
                    <option value="" disabled {{ old('idCicloEscolar') ? '' : 'selected' }}>Seleccionar</option>
                    @foreach($ciclos as $ciclo)
                        <option value="{{ $ciclo->idCicloEscolar }}" {{ old('idCicloEscolar') == $ciclo->idCicloEscolar ? 'selected' : '' }}>
                            {{ $ciclo->nombreCicloEscolar }}
                        </option>
                    @endforeach
                </select>
                <x-error-field field="idCicloEscolar" />
            </div>

            <div class="form-group">
                <label for="idBloque">Bloque:</label>
                <select id="idBloque" name="idBloque" class="select" required disabled>
                    <option value="" disabled selected>Seleccionar</option>
                </select>
                <x-error-field field="idBloque" />
            </div>

            <div class="horarios-bloque">
                <h2 class="consulta-titulo consulta-subtitulo">Distribucion semanal</h2>

                <section class="consulta-tabla-contenedor2">
                    <table class="tabla">
                        <thead>
                            <tr class="tabla-encabezado">
                                @foreach($dias as $dia)
                                    <th>{{ $dia->nombreDia }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="tabla-cuerpo">
                            <tr>
                                @foreach($dias as $dia)
                                    <td>
                                        <div class="horario-columna-wrap">
                                            <div class="horario-columna" data-dia="{{ $dia->idDiaSemana }}"></div>
                                            <button type="button"
                                                class="btn-boton-formulario2 btn-boton-formulario2--auto horario-columna-accion"
                                                onclick="agregarFila('{{ $dia->idDiaSemana }}')">
                                                Agregar
                                            </button>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </section>
            </div>

            <div id="horariosHidden"></div>

            <div class="form-group">
                <button type="submit" class="btn-boton-formulario">Guardar</button>
                <a href="{{ route('apartadoHorarios') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
            </div>
        </form>
    </main>

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

    <template id="filaHorarioTemplate">
        <div class="horario-row">
            <div class="form-group">
                <label>Asignatura</label>
                <select class="select input-asignatura">
                    <option value="">Seleccionar</option>
                    @foreach($asignaturas as $asignatura)
                        <option value="{{ $asignatura->idAsignatura }}">{{ $asignatura->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Inicio</label>
                <input type="time" class="input-chico input-inicio">
            </div>

            <div class="form-group">
                <label>Fin</label>
                <input type="time" class="input-chico input-fin">
            </div>

            <div class="form-group">
                <label>Docente</label>
                <select class="select input-docente">
                    <option value="">Opcional</option>
                    @foreach($docentes as $docente)
                        <option value="{{ $docente->idDocente }}">{{ $docente->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Aula</label>
                <select class="select input-aula">
                    <option value="">Opcional</option>
                    @foreach($aulas as $aula)
                        <option value="{{ $aula->idAula }}">{{ $aula->nombreAula }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group horario-fila-acciones">
                <button type="button" class="btn-boton-formulario2 btn-boton-formulario2--auto btn-guardar" disabled>
                    Guardar
                </button>
                <button type="button" class="btn-boton-formulario2 btn-boton-formulario2--auto btn-cancelar2 btn-eliminar">
                    Quitar
                </button>
                <span class="horario-etiqueta-guardado" hidden>Guardado</span>
            </div>
        </div>
    </template>

    <script>
        const bloques = @json($bloques);

        document.addEventListener('DOMContentLoaded', function () {
            const cicloSelect = document.getElementById('idCicloEscolar');
            const bloqueSelect = document.getElementById('idBloque');
            const oldCiclo = "{{ old('idCicloEscolar') }}";
            const oldBloque = "{{ old('idBloque') }}";

            const resetBloques = (placeholder, disabled = true) => {
                bloqueSelect.innerHTML = `<option value="">${placeholder}</option>`;
                bloqueSelect.disabled = disabled;
            };

            const cargarBloques = (idCiclo) => {
                const opciones = bloques.filter(b => String(b.idCicloEscolar) === String(idCiclo));
                resetBloques('Seleccionar', opciones.length === 0);

                opciones.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.idBloque;
                    option.textContent = `BLOQUE ${item.numeroBloque} ${item.nombreModalidad}`;
                    if (oldBloque && String(oldBloque) === String(item.idBloque)) {
                        option.selected = true;
                    }
                    bloqueSelect.appendChild(option);
                });
            };

            if (oldCiclo) {
                cicloSelect.value = oldCiclo;
                cargarBloques(oldCiclo);
            } else {
                resetBloques('Seleccionar', true);
            }

            cicloSelect.addEventListener('change', function () {
                if (!this.value) {
                    resetBloques('Seleccionar', true);
                    return;
                }
                cargarBloques(this.value);
            });
        });

        function agregarFila(idDia) {
            const template = document.getElementById('filaHorarioTemplate');
            const container = document.querySelector(`.horario-columna[data-dia="${idDia}"]`);
            if (!template || !container) return;

            const nodo = template.content.cloneNode(true);
            const row = nodo.querySelector('.horario-row');
            row.dataset.dia = idDia;

            const asignatura = row.querySelector('.input-asignatura');
            const inicio = row.querySelector('.input-inicio');
            const fin = row.querySelector('.input-fin');
            const docente = row.querySelector('.input-docente');
            const aula = row.querySelector('.input-aula');
            const guardar = row.querySelector('.btn-guardar');
            const eliminar = row.querySelector('.btn-eliminar');
            const etiqueta = row.querySelector('.horario-etiqueta-guardado');

            const validar = () => {
                const ok = asignatura.value && inicio.value && fin.value && inicio.value < fin.value;
                guardar.disabled = !ok;
            };

            [asignatura, inicio, fin].forEach(el => el.addEventListener('change', validar));
            [asignatura, inicio, fin].forEach(el => el.addEventListener('keyup', validar));

            guardar.addEventListener('click', function () {
                const payload = {
                    idDiaSemana: parseInt(idDia, 10),
                    idAsignatura: parseInt(asignatura.value, 10),
                    hora_inicio: inicio.value,
                    hora_fin: fin.value,
                    idDocente: docente.value ? parseInt(docente.value, 10) : null,
                    idAula: aula.value ? parseInt(aula.value, 10) : null,
                };

                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'horarios[]';
                hidden.value = JSON.stringify(payload);
                document.getElementById('horariosHidden').appendChild(hidden);

                [asignatura, inicio, fin, docente, aula].forEach(el => {
                    el.setAttribute('disabled', 'disabled');
                });
                guardar.setAttribute('disabled', 'disabled');
                if (etiqueta) {
                    etiqueta.hidden = false;
                }
            });

            eliminar.addEventListener('click', function () {
                row.remove();
            });

            container.appendChild(nodo);
        }
    </script>
</body>
</html>
