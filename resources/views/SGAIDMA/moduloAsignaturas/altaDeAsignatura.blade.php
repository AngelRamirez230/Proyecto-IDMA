<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de asignatura</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    @php
        $esEdicion = isset($asignatura);
        $titulo = $esEdicion ? 'Edicion de asignatura' : 'Alta de asignatura';
        $accion = $esEdicion
            ? route('asignaturas.update', $asignatura->idAsignatura)
            : route('asignaturas.store');
        $cancelar = $esEdicion
            ? route('consultaAsignatura')
            : route('apartadoAsignaturas');
    @endphp

    <main class="form-container">
        <form action="{{ $accion }}" method="POST" class="formulario" enctype="multipart/form-data">
            @csrf
            @if($esEdicion)
                @method('PUT')
            @endif

            <h1 class="titulo-form">{{ $titulo }}</h1>

            <div class="form-group">
                <label for="nombre">Nombre de la asignatura:</label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    class="input-grande"
                    placeholder="Ingresa el nombre de la asignatura"
                    value="{{ old('nombre', $asignatura->nombre ?? '') }}"
                    required
                >
                <x-error-field field="nombre" />
            </div>

            <div class="form-group">
                <label for="claveAsignatura">Clave o código:</label>
                <input
                    type="text"
                    id="claveAsignatura"
                    name="claveAsignatura"
                    class="input-mediano"
                    placeholder="Ingresa la clave de la asignatura"
                    value="{{ old('claveAsignatura', $asignatura->claveAsignatura ?? '') }}"
                    required
                >
                <x-error-field field="claveAsignatura" />
            </div>

            <div class="form-group">
                <label for="creditos">Créditos:</label>
                <input
                    type="number"
                    id="creditos"
                    name="creditos"
                    class="input-chico"
                    min="1"
                    step="1"
                    value="{{ old('creditos', $asignatura->creditos ?? '') }}"
                    required
                >
                <x-error-field field="creditos" />
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
                    value="{{ old('semestre', $asignatura->semestre ?? '') }}"
                    required
                >
                <x-error-field field="semestre" />
            </div>

            <div class="form-group">
                <label for="horasConDocente">Horas con docente:</label>
                <input
                    type="number"
                    id="horasConDocente"
                    name="horasConDocente"
                    class="input-chico"
                    min="0"
                    step="1"
                    value="{{ old('horasConDocente', $asignatura->horasConDocente ?? '') }}"
                    required
                >
                <x-error-field field="horasConDocente" />
            </div>

            <div class="form-group">
                <label for="horasIndependientes">Horas independientes:</label>
                <input
                    type="number"
                    id="horasIndependientes"
                    name="horasIndependientes"
                    class="input-chico"
                    min="0"
                    step="1"
                    value="{{ old('horasIndependientes', $asignatura->horasIndependientes ?? '') }}"
                    required
                >
                <x-error-field field="horasIndependientes" />
            </div>

            <div class="form-group">
                <label for="idNivelDeFormacion">Nivel de formación:</label>
                <select id="idNivelDeFormacion" name="idNivelDeFormacion" class="select" required>
                    <option value="" disabled {{ old('idNivelDeFormacion', $asignatura->idNivelDeFormacion ?? '') ? '' : 'selected' }}>
                        Seleccionar
                    </option>
                    @foreach($niveles as $nivel)
                        <option
                            value="{{ $nivel->idNivel_de_formacion }}"
                            {{ old('idNivelDeFormacion', $asignatura->idNivelDeFormacion ?? '') == $nivel->idNivel_de_formacion ? 'selected' : '' }}
                        >
                            {{ $nivel->nombreNivel }}
                        </option>
                    @endforeach
                </select>
                <x-error-field field="idNivelDeFormacion" />
            </div>

            <div class="form-group">
                <label for="idPlanDeEstudios">Plan de estudios:</label>
                <select id="idPlanDeEstudios" name="idPlanDeEstudios" class="select" required>
                    <option value="" disabled {{ old('idPlanDeEstudios', $asignatura->idPlanDeEstudios ?? '') ? '' : 'selected' }}>
                        Seleccionar
                    </option>
                    @foreach($planes as $plan)
                        <option
                            value="{{ $plan->idPlanDeEstudios }}"
                            {{ old('idPlanDeEstudios', $asignatura->idPlanDeEstudios ?? '') == $plan->idPlanDeEstudios ? 'selected' : '' }}
                        >
                            {{ $plan->nombrePlanDeEstudios }}
                            @if($plan->licenciatura)
                                - {{ $plan->licenciatura->nombreLicenciatura }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <x-error-field field="idPlanDeEstudios" />
            </div>

            {{--<div class="form-group">
                <label for="documentoAsignatura">Documento de la asignatura:</label>
                <input
                    type="file"
                    id="documentoAsignatura"
                    name="documentoAsignatura"
                    class="input-mediano"
                    accept=".pdf,.doc,.docx"
                >
                <x-error-field field="documentoAsignatura" />
            </div>--}}

            <div class="form-group">
                <button type="submit" class="btn-boton-formulario">Guardar</button>
                <a href="{{ $cancelar }}" class="btn-boton-formulario btn-cancelar">
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
