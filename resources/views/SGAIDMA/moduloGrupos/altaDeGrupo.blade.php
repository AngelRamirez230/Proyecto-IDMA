<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Grupo</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    @include('layouts.barraNavegacion')

    <main class="form-container">
        <form action="{{ route('grupos.store') }}" method="POST" class="formulario">
            @csrf

            <h1 class="titulo-form">Alta de grupo</h1>

            <div class="form-group">
                <label for="nombreGrupo">Nombre del grupo:</label>
                <input
                    type="text"
                    id="nombreGrupo"
                    name="nombreGrupo"
                    class="input-grande"
                    placeholder="Ingresa el nombre del grupo"
                    value="{{ old('nombreGrupo') }}"
                    required
                >
                <x-error-field field="nombreGrupo" />
            </div>

            <div class="form-group">
                <label for="claveGrupo">Clave del grupo:</label>
                <input
                    type="text"
                    id="claveGrupo"
                    name="claveGrupo"
                    class="input-mediano"
                    placeholder="Ingresa la clave del grupo"
                    value="{{ old('claveGrupo') }}"
                    required
                >
                <x-error-field field="claveGrupo" />
            </div>

            <div class="form-group">
                <label for="idLicenciatura">Carrera:</label>
                <select id="idLicenciatura" name="idLicenciatura" class="select" required>
                    <option value="" disabled {{ old('idLicenciatura') ? '' : 'selected' }}>
                        Seleccionar
                    </option>
                    @foreach($licenciaturas as $licenciatura)
                        <option
                            value="{{ $licenciatura->idLicenciatura }}"
                            {{ old('idLicenciatura') == $licenciatura->idLicenciatura ? 'selected' : '' }}
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
                    value="{{ old('semestre') }}"
                    required
                >
                <x-error-field field="semestre" />
            </div>


            <div class="form-group">
                <label for="idModalidad">Modalidad:</label>
                <select id="idModalidad" name="idModalidad" class="select" required>
                    <option value="" disabled {{ old('idModalidad') ? '' : 'selected' }}>
                        Seleccionar
                    </option>
                    @foreach($modalidades as $modalidad)
                        <option
                            value="{{ $modalidad->idModalidad }}"
                            {{ old('idModalidad') == $modalidad->idModalidad ? 'selected' : '' }}
                        >
                            {{ $modalidad->nombreModalidad }}
                        </option>
                    @endforeach
                </select>
                <x-error-field field="idModalidad" />
            </div>

            <div class="form-group">
                <label for="periodoAcademico">Periodo academico:</label>
                <input
                    type="text"
                    id="periodoAcademico"
                    name="periodoAcademico"
                    class="input-mediano"
                    placeholder="Ej. 2024-2025"
                    value="{{ old('periodoAcademico') }}"
                    required
                >
                <x-error-field field="periodoAcademico" />
            </div>

            <div class="form-group">
                <button type="submit" class="btn-boton-formulario">Guardar</button>
                <a href="{{ route('apartadoGrupos') }}" class="btn-boton-formulario btn-cancelar">
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
</body>
</html>
