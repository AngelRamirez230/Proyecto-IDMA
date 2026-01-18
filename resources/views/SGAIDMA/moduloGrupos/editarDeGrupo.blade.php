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
</body>
</html>
