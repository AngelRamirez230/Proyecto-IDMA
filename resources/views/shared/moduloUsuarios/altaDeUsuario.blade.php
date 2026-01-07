<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de usuario</title>
    @vite(['resources/css/app.css'])
</head>

<body>
@include('layouts.barraNavegacion')

<main class="form-container">

    {{-- FORMULARIO --}}
    <form action="{{ route('usuarios.store') }}" method="POST" class="formulario">
        @csrf

        {{-- TÍTULO DEL FORMULARIO --}}
    <h1 class="titulo-form">
        Alta de usuario - Rol:
        <strong>
            @if(isset($rol) && $rol == 1)
                Administrador
            @elseif(isset($rol) && $rol == 2)
                Empleado
            @elseif(isset($rol) && $rol == 3)
                Docente
            @elseif(isset($rol) && $rol == 4)
                Estudiante
            @else
                Sin rol definido
            @endif
        </strong>
    </h1>


        {{-- ENVIAR ROL SELECCIONADO --}}
        <input type="hidden" name="rol" value="{{ $rol ?? '' }}">

        <h3 class="subtitulo-form">Datos personales</h3>

            {{-- PRIMER NOMBRE --}}
    <div class="form-group">
        <label for="primer_nombre">Primer nombre:</label>
        <input
            type="text"
            id="primer_nombre"
            name="primer_nombre"
            class="input-mediano"
            placeholder="Ingresa el primer nombre"
            value="{{ old('primer_nombre') }}"
            required
        >
        <x-error-field field="primer_nombre" />
    </div>

    {{-- SEGUNDO NOMBRE --}}
    <div class="form-group">
        <label for="segundo_nombre">Segundo nombre:</label>
        <input
            type="text"
            id="segundo_nombre"
            name="segundo_nombre"
            class="input-mediano"
            placeholder="Ingresa el segundo nombre"
            value="{{ old('segundo_nombre') }}"
        >
        <x-error-field field="segundo_nombre" />
    </div>

    {{-- PRIMER APELLIDO --}}
    <div class="form-group">
        <label for="primer_apellido">Primer apellido:</label>
        <input
            type="text"
            id="primer_apellido"
            name="primer_apellido"
            class="input-mediano"
            placeholder="Ingresa el primer apellido"
            value="{{ old('primer_apellido') }}"
            required
        >
        <x-error-field field="primer_apellido" />
    </div>

    {{-- SEGUNDO APELLIDO --}}
    <div class="form-group">
        <label for="segundo_apellido">Segundo apellido:</label>
        <input
            type="text"
            id="segundo_apellido"
            name="segundo_apellido"
            class="input-mediano"
            placeholder="Ingresa el segundo apellido"
            value="{{ old('segundo_apellido') }}"
        >
        <x-error-field field="segundo_apellido" />
    </div>

    {{-- SEXO --}}
    <div class="form-group">
        <label for="sexo">Sexo:</label>
        <select id="sexo" name="sexo" class="select" required>
            <option value="" disabled {{ old('sexo') ? '' : 'selected' }}>Seleccionar</option>
            @foreach($sexos as $sx)
                <option value="{{ $sx->idSexo }}" {{ old('sexo') == $sx->idSexo ? 'selected' : '' }}>
                    {{ $sx->nombreSexo }}
                </option>
            @endforeach
        </select>
        <x-error-field field="sexo" />
    </div>

    {{-- ESTADO CIVIL --}}
    <div class="form-group">
        <label for="estadoCivil">Estado civil:</label>
        <select id="estadoCivil" name="estadoCivil" class="select" required>
            <option value="" disabled {{ old('estadoCivil') ? '' : 'selected' }}>Seleccionar</option>
            @foreach($estadosCiviles as $ec)
                <option value="{{ $ec->idEstadoCivil }}" {{ old('estadoCivil') == $ec->idEstadoCivil ? 'selected' : '' }}>
                    {{ $ec->nombreEstadoCivil }}
                </option>
            @endforeach
        </select>
        <x-error-field field="estadoCivil" />
    </div>

    {{-- TELÉFONO --}}
    <div class="form-group">
        <label for="telefono">Teléfono:</label>
        <input
            type="text"
            id="telefono"
            name="telefono"
            class="input-chico"
            placeholder="Ingresa número de teléfono"
            value="{{ old('telefono') }}"
        >
        <x-error-field field="telefono" />
    </div>

    {{-- TELÉFONO FIJO --}}
    <div class="form-group">
        <label for="telefonoFijo">Teléfono fijo:</label>
        <input
            type="text"
            id="telefonoFijo"
            name="telefonoFijo"
            class="input-chico"
            placeholder="Teléfono fijo"
            value="{{ old('telefonoFijo') }}"
        >
        <x-error-field field="telefonoFijo" />
    </div>

    {{-- CORREO INSTITUCIONAL --}}
    <div class="form-group">
        <label for="emailInstitucional">Correo institucional:</label>
        <input
            type="email"
            id="emailInstitucional"
            name="emailInstitucional"
            class="input-mediano"
            placeholder="ejemplo@idma.edu.mx"
            value="{{ old('emailInstitucional') }}"
        >
        <x-error-field field="emailInstitucional" />
    </div>

    {{-- CONTRASEÑA --}}
    <div class="form-group">
        <label for="password">Contraseña:</label>
        <input
            type="password"
            id="password"
            name="password"
            class="input-chico"
            placeholder="Escribe una contraseña"
            required
        >
        <x-error-field field="password" />
    </div>

    {{-- NOMBRE DE USUARIO --}}
    <div class="form-group">
        <label for="nombreUsuario">Nombre de usuario:</label>
        <input
            type="text"
            id="nombreUsuario"
            name="nombreUsuario"
            class="input-mediano"
            placeholder="Ingresa el nombre de usuario"
            value="{{ old('nombreUsuario') }}"
            required
        >
        <x-error-field field="nombreUsuario" />
    </div>

    {{-- FECHA NACIMIENTO --}}
    <div class="form-group">
        <label for="fechaNacimiento">Fecha de nacimiento:</label>
        <input
            type="date"
            id="fechaNacimiento"
            name="fechaNacimiento"
            max="{{ date('Y-m-d') }}"
            class="input-date"
            value="{{ old('fechaNacimiento') }}"
        >
        <x-error-field field="fechaNacimiento" />
    </div>

    {{-- CURP --}}
    <div class="form-group">
        <label for="curp">CURP:</label>
        <input
            type="text"
            id="curp"
            name="curp"
            class="input-chico"
            placeholder="CURP"
            value="{{ old('curp') }}"
        >
        <x-error-field field="curp" />
    </div>

    {{-- RFC --}}
    <div class="form-group">
        <label for="rfc">RFC:</label>
        <input
            type="text"
            id="rfc"
            name="rfc"
            class="input-chico"
            placeholder="RFC"
            value="{{ old('rfc') }}"
        >
        <x-error-field field="rfc" />
    </div>

    {{-- CORREO PERSONAL --}}
    <div class="form-group">
        <label for="email">Correo electrónico:</label>
        <input
            type="email"
            id="email"
            name="email"
            class="input-mediano"
            placeholder="ejemplo@correo.com"
            value="{{ old('email') }}"
        >
        <x-error-field field="email" />
    </div>

    @if(isset($rol) && (int) $rol === 2)
        <h3 class="subtitulo-form">Datos del empleado</h3>

        <div class="form-group">
            <label for="idDepartamento">Departamento:</label>
            <select id="idDepartamento" name="idDepartamento" class="select" required>
                <option value="" disabled {{ old('idDepartamento') ? '' : 'selected' }}>Seleccionar</option>
                @foreach($departamentos as $departamento)
                    <option value="{{ $departamento->idDepartamento }}" {{ old('idDepartamento') == $departamento->idDepartamento ? 'selected' : '' }}>
                        {{ $departamento->nombreDepartamento ?? ('Departamento #' . $departamento->idDepartamento) }}
                    </option>
                @endforeach
            </select>
            <x-error-field field="idDepartamento" />
        </div>

        <div class="form-group">
            <label for="idNivelAcademico">Nivel académico:</label>
            <select id="idNivelAcademico" name="idNivelAcademico" class="select" required>
                <option value="" disabled {{ old('idNivelAcademico') ? '' : 'selected' }}>Seleccionar</option>
                @foreach($nivelesAcademicos as $nivel)
                    <option value="{{ $nivel->idNivelAcademico }}" {{ old('idNivelAcademico') == $nivel->idNivelAcademico ? 'selected' : '' }}>
                        {{ $nivel->nombreNivelAcademico }}{{ $nivel->abreviacionNombre ? ' (' . $nivel->abreviacionNombre . ')' : '' }}
                    </option>
                @endforeach
            </select>
            <x-error-field field="idNivelAcademico" />
        </div>
    @endif

    @if(isset($rol) && (int) $rol === 3)
        <h3 class="subtitulo-form">Datos del docente y disponibilidad de horario</h3>

        <div class="form-group">
            <label for="idNivelAcademicoDocente">Nivel academico:</label>
            <select id="idNivelAcademicoDocente" name="idNivelAcademico" class="select" required>
                <option value="" disabled {{ old('idNivelAcademico') ? '' : 'selected' }}>Seleccionar</option>
                @foreach($nivelesAcademicos as $nivel)
                    <option value="{{ $nivel->idNivelAcademico }}" {{ old('idNivelAcademico') == $nivel->idNivelAcademico ? 'selected' : '' }}>
                        {{ $nivel->nombreNivelAcademico }}{{ $nivel->abreviacionNombre ? ' (' . $nivel->abreviacionNombre . ')' : '' }}
                    </option>
                @endforeach
            </select>
            <x-error-field field="idNivelAcademico" />
        </div>

        @php
            $horariosOld = old('horarios');
            if (!is_array($horariosOld) || count($horariosOld) === 0) {
                $horariosOld = [
                    [
                        'idDiaSemana' => '',
                        'idRangoDeHorario' => '',
                        'horaInicio' => '',
                        'horaFin' => '',
                    ],
                ];
            }
        @endphp

        <div id="horarios-container" class="docente-horarios">
            @foreach($horariosOld as $index => $horario)
                @php
                    $usaManual = empty($horario['idRangoDeHorario'])
                        && (!empty($horario['horaInicio']) || !empty($horario['horaFin']));
                @endphp
                <div class="horario-row" data-index="{{ $index }}">
                    <div class="form-group docente-nivel">
                        <label>Día:</label>
                        <select name="horarios[{{ $index }}][idDiaSemana]" class="select" required>
                            <option value="" disabled {{ !empty($horario['idDiaSemana']) ? '' : 'selected' }}>Seleccionar</option>
                            @foreach($diasSemana as $dia)
                                <option value="{{ $dia->idDiaSemana }}" {{ (string)($horario['idDiaSemana'] ?? '') === (string)$dia->idDiaSemana ? 'selected' : '' }}>
                                    {{ $dia->nombreDia }}
                                </option>
                            @endforeach
                        </select>
                        <x-error-field field="horarios.{{ $index }}.idDiaSemana" />
                    </div>

                    <div class="form-group docente-nivel">
                        <label>Horario:</label>
                        <select name="horarios[{{ $index }}][idRangoDeHorario]" class="select rango-select">
                            <option value="" {{ empty($horario['idRangoDeHorario']) && !$usaManual ? 'selected' : '' }}>Seleccionar</option>
                            @foreach($rangosHorarios as $rango)
                                <option value="{{ $rango->idRangoDeHorario }}" {{ (string)($horario['idRangoDeHorario'] ?? '') === (string)$rango->idRangoDeHorario ? 'selected' : '' }}>
                                    {{ $rango->horaInicio }} - {{ $rango->horaFin }}
                                </option>
                            @endforeach
                            <option value="manual" {{ $usaManual ? 'selected' : '' }}>Otro horario...</option>
                        </select>
                        <x-error-field field="horarios.{{ $index }}.idRangoDeHorario" />
                    </div>

                    <div class="form-group horario-manual" style="{{ $usaManual ? '' : 'display:none;' }}">
                        <label>Hora inicio:</label>
                        <input
                            type="time"
                            name="horarios[{{ $index }}][horaInicio]"
                            class="input-chico"
                            value="{{ $horario['horaInicio'] ?? '' }}"
                        >
                        <x-error-field field="horarios.{{ $index }}.horaInicio" />
                    </div>

                    <div class="form-group horario-manual" style="{{ $usaManual ? '' : 'display:none;' }}">
                        <label>Hora fin:</label>
                        <input
                            type="time"
                            name="horarios[{{ $index }}][horaFin]"
                            class="input-chico"
                            value="{{ $horario['horaFin'] ?? '' }}"
                        >
                        <x-error-field field="horarios.{{ $index }}.horaFin" />
                    </div>

                    <div class="form-group docente-nivel">
                        <button type="button" class="btn-boton-formulario btn-cancelar btn-quitar-horario">
                            Quitar
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="form-group docente-horarios-acciones">
            <button type="button" class="btn-boton-formulario" id="agregar-horario">
                Agregar horario
            </button>
        </div>
        <x-error-field field="horarios" />

        <template id="horario-template">
            <div class="horario-row" data-index="__INDEX__">
                <div class="form-group docente-nivel">
                    <label>Dia:</label>
                    <select name="horarios[__INDEX__][idDiaSemana]" class="select" required>
                        <option value="" disabled selected>Seleccionar</option>
                        @foreach($diasSemana as $dia)
                            <option value="{{ $dia->idDiaSemana }}">{{ $dia->nombreDia }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group docente-nivel">
                    <label>Horario:</label>
                    <select name="horarios[__INDEX__][idRangoDeHorario]" class="select rango-select">
                        <option value="" selected>Seleccionar</option>
                        @foreach($rangosHorarios as $rango)
                            <option value="{{ $rango->idRangoDeHorario }}">
                                {{ $rango->horaInicio }} - {{ $rango->horaFin }}
                            </option>
                        @endforeach
                        <option value="manual">Otro horario...</option>
                    </select>
                </div>

                <div class="form-group horario-manual" style="display:none;">
                    <label>Hora inicio:</label>
                    <input type="time" name="horarios[__INDEX__][horaInicio]" class="input-chico">
                </div>

                <div class="form-group horario-manual" style="display:none;">
                    <label>Hora fin:</label>
                    <input type="time" name="horarios[__INDEX__][horaFin]" class="input-chico">
                </div>

                <div class="form-group docente-nivel">
                    <button type="button" class="btn-boton-formulario btn-cancelar btn-quitar-horario">
                        Quitar
                    </button>
                </div>
            </div>
        </template>
    @endif

    <h3 class="subtitulo-form">Datos del domicilio</h3>

        {{-- ENTIDAD --}}
    <div class="form-group">
        <label>Entidad:</label>
        <select id="entidad" name="entidad" class="select select-buscable">
            <option value="">Seleccionar</option>
            @foreach($entidades as $e)
                <option value="{{ $e->idEntidad }}">{{ $e->nombreEntidad }}</option>
            @endforeach
        </select>
        <x-error-field field="entidad" />
    </div>

    {{-- MUNICIPIO --}}
    <div class="form-group">
        <label>Municipio:</label>

        <div class="select-buscable-wrapper">
            <input
                type="text"
                class="input-mediano select-buscable-input"
                placeholder="Seleccione entidad"
                data-target="municipio"
                autocomplete="off"
                readonly
            >

            <ul class="select-buscable-list"></ul>

            <select
                id="municipio"
                name="municipio"
                required
                hidden
                disabled
            >
                <option value="">Seleccionar</option>
            </select>
        </div>

        <x-error-field field="municipio" />
    </div>

    {{-- LOCALIDAD --}}
    <div class="form-group">
        <label>Localidad:</label>

        <div class="select-buscable-wrapper">
            <input
                type="text"
                class="input-mediano select-buscable-input"
                placeholder="Buscar localidad..."
                data-target="localidad"
                autocomplete="off"
                readonly
            >

            <ul class="select-buscable-list"></ul>

            <select
                id="localidad"
                name="localidad"
                required
                hidden
                disabled
            >
                <option value="">Seleccionar</option>
            </select>
        </div>

        <x-error-field field="localidad" />
    </div>

    <div class="form-group" id="localidadManualDomicilio" style="display:none;">
        <label for="localidadManual">Localidad (manual):</label>
        <input
            type="text"
            id="localidadManual"
            name="localidadManual"
            class="input-mediano"
            placeholder="Escribe la localidad"
            value="{{ old('localidadManual') }}"
        >
        <x-error-field field="localidadManual" />
    </div>

    {{-- COLONIA --}}
    <div class="form-group">
        <label for="colonia">Colonia:</label>
        <input
            type="text"
            id="colonia"
            name="colonia"
            class="input-mediano"
            placeholder="Colonia"
            value="{{ old('colonia') }}"
        >
        <x-error-field field="colonia" />
    </div>

    {{-- CÓDIGO POSTAL --}}
    <div class="form-group">
        <label for="codigoPostal">Código postal:</label>
        <input
            type="text"
            id="codigoPostal"
            name="codigoPostal"
            class="input-chico"
            placeholder="Código postal"
            value="{{ old('codigoPostal') }}"
        >
        <x-error-field field="codigoPostal" />
    </div>

    {{-- CALLE --}}
    <div class="form-group">
        <label for="calle">Calle:</label>
        <input
            type="text"
            id="calle"
            name="calle"
            class="input-grande"
            placeholder="Ingresa la calle"
            value="{{ old('calle') }}"
        >
        <x-error-field field="calle" />
    </div>

    {{-- NÚMERO EXTERIOR --}}
    <div class="form-group">
        <label for="numeroExterior">Número exterior:</label>
        <input
            type="text"
            id="numeroExterior"
            name="numeroExterior"
            class="input-chico"
            placeholder="Número exterior"
            value="{{ old('numeroExterior') }}"
        >
        <x-error-field field="numeroExterior" />
    </div>

    {{-- NÚMERO INTERIOR --}}
    <div class="form-group">
        <label for="numeroInterior">Número interior:</label>
        <input
            type="text"
            id="numeroInterior"
            name="numeroInterior"
            class="input-chico"
            placeholder="Número interior"
            value="{{ old('numeroInterior') }}"
        >
        <x-error-field field="numeroInterior" />
    </div>

    <h3 class="subtitulo-form">Lugar de nacimiento</h3>

    <div class="form-group">
        <label for="paisNacimiento">País:</label>
        <select id="paisNacimiento" name="paisNacimiento" class="select select-buscable" required>
            <option value="">Seleccionar</option>
            @foreach($paises as $pais)
                <option
                    value="{{ $pais->idPais }}"
                    data-normalizado="{{ $pais->nombrePaisNormalizado }}"
                    {{ old('paisNacimiento') == $pais->idPais ? 'selected' : '' }}
                >
                    {{ $pais->nombrePais }}
                </option>
            @endforeach
        </select>
        <x-error-field field="paisNacimiento" />
    </div>

    <div id="bloque-select-nacimiento">

        {{-- ENTIDAD --}}
        <div class="form-group">
            <label>Entidad de nacimiento:</label>
            <select id="entidadNacimientoSelect" name="entidadNacimiento" class="select select-buscable">
                <option value="">Seleccionar entidad</option>
                @foreach($entidades as $e)
                    <option value="{{ $e->idEntidad }}">{{ $e->nombreEntidad }}</option>
                @endforeach
            </select>
            <x-error-field field="entidadNacimiento" />
        </div>

        {{-- MUNICIPIO (BUSCABLE) --}}
        <div class="form-group">
            <label>Municipio de nacimiento:</label>

            <div class="select-buscable-wrapper">
                <input
                    type="text"
                    class="input-mediano select-buscable-input"
                    placeholder="Seleccione entidad"
                    data-target="municipioNacimientoSelect"
                    autocomplete="off"
                    readonly
                >

                <ul class="select-buscable-list"></ul>

                <select
                    id="municipioNacimientoSelect"
                    name="municipioNacimiento"
                    hidden
                    disabled
                >
                    <option value="">Seleccionar</option>
                </select>
            </div>

            <x-error-field field="municipioNacimiento" />
        </div>

        {{-- LOCALIDAD (BUSCABLE) --}}
        <div class="form-group">
            <label>Localidad de nacimiento:</label>

            <div class="select-buscable-wrapper">
                <input
                    type="text"
                    class="input-mediano select-buscable-input"
                    placeholder="Seleccione municipio"
                    data-target="localidadNacimientoSelect"
                    autocomplete="off"
                    readonly
                >

                <ul class="select-buscable-list"></ul>

                <select
                    id="localidadNacimientoSelect"
                    name="localidadNacimiento"
                    hidden
                    disabled
                >
                    <option value="">Seleccionar</option>
                </select>
            </div>

            <x-error-field field="localidadNacimiento" />
        </div>
    </div>

    <div id="bloque-input-nacimiento" style="display:none;">

        <div class="form-group">
            <label>Entidad de nacimiento:</label>
            <input
                type="text"
                name="entidadNacimientoManual"
                class="input-mediano"
                placeholder="Escribe la entidad"
                value="{{ old('entidadNacimientoManual') }}"
                disabled
            >
            <x-error-field field="entidadNacimientoManual" />
        </div>

        <div class="form-group">
            <label>Municipio de nacimiento:</label>
            <input
                type="text"
                name="municipioNacimientoManual"
                class="input-mediano"
                placeholder="Escribe el municipio"
                value="{{ old('municipioNacimientoManual') }}"
                disabled
            >
            <x-error-field field="municipioNacimientoManual" />
        </div>

        <div class="form-group">
            <label>Localidad de nacimiento:</label>
            <input
                type="text"
                name="localidadNacimientoManual"
                class="input-mediano"
                placeholder="Escribe la localidad"
                value="{{ old('localidadNacimientoManual') }}"
                disabled
            >
            <x-error-field field="localidadNacimientoManual" />
        </div>
    </div>

    {{-- BOTONES --}}
    <div class="form-group form-botones">
        <button type="submit" class="btn-boton-formulario">Guardar</button>
        <a href="{{ route('apartadoUsuarios') }}" class="btn-boton-formulario btn-cancelar">
            Cancelar
        </a>
    </div>

</main>

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

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* =========================================================
       HELPERS GENERALES
    ========================================================= */
    const resetSelect = (select, placeholder, disabled = true) => {
        if (!select) return;
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled = disabled;
    };

    const fillSelect = (select, placeholder, data, valueKey, textKey) => {
        if (!select) return;
        select.innerHTML = `<option value="">${placeholder}</option>`;
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item[valueKey];
            opt.textContent = item[textKey];
            select.appendChild(opt);
        });
        select.disabled = false;
    };

    const setFirstOptionText = (select, text) => {
        if (!select || !select.options || select.options.length === 0) return;
        select.options[0].textContent = text;
    };

    const getWrapperInput = (selectEl) => {
        return selectEl
            ?.closest('.select-buscable-wrapper')
            ?.querySelector('.select-buscable-input') || null;
    };

    /* =========================================================
       DOMICILIO
       entidad → municipio → localidad
    ========================================================= */
    const domEntidad   = document.getElementById('entidad');
    const domMunicipio = document.getElementById('municipio');
    const domLocalidad = document.getElementById('localidad');

    if (domEntidad && domMunicipio && domLocalidad) {

        const municipioInput = getWrapperInput(domMunicipio);
        const localidadInput = getWrapperInput(domLocalidad);

        /* ===== ESTADO INICIAL ===== */
        if (municipioInput) {
            municipioInput.placeholder = 'Seleccione entidad';
            municipioInput.setAttribute('readonly', 'readonly');
        }

        if (localidadInput) {
            localidadInput.placeholder = 'Seleccione municipio';
            localidadInput.setAttribute('readonly', 'readonly');
        }

        /* ===== ENTIDAD → MUNICIPIOS ===== */
        domEntidad.addEventListener('change', () => {
            const idEntidad = domEntidad.value;

            resetSelect(domMunicipio, 'Seleccionar', true);
            resetSelect(domLocalidad, 'Selecciona un municipio', true);

            if (municipioInput) {
                municipioInput.value = '';
                municipioInput.placeholder = idEntidad ? 'Buscar municipio...' : 'Seleccione entidad';
                if (!idEntidad) municipioInput.setAttribute('readonly', 'readonly');
            }

            if (localidadInput) {
                localidadInput.value = '';
                localidadInput.placeholder = 'Seleccione municipio';
                localidadInput.setAttribute('readonly', 'readonly');
            }

            if (!idEntidad) return;

            fetch(`/api/municipios/${idEntidad}`)
                .then(r => r.json())
                .then(data => {
                    fillSelect(domMunicipio, 'Seleccionar', data, 'idMunicipio', 'nombreMunicipio');

                    if (municipioInput) {
                        municipioInput.placeholder = 'Buscar municipio...';
                        municipioInput.removeAttribute('readonly');
                    }
                });
        });

        /* ===== MUNICIPIO → LOCALIDADES ===== */
        domMunicipio.addEventListener('change', () => {
            const idMunicipio = domMunicipio.value;

            resetSelect(domLocalidad, 'Seleccionar', true);

            if (localidadInput) {
                localidadInput.value = '';
                localidadInput.placeholder = idMunicipio ? 'Buscar localidad...' : 'Seleccione municipio';
                if (!idMunicipio) localidadInput.setAttribute('readonly', 'readonly');
            }

            if (!idMunicipio) return;

            fetch(`/api/localidades/${idMunicipio}`)
                .then(r => r.json())
                .then(data => {
                    fillSelect(domLocalidad, 'Seleccionar', data, 'idLocalidad', 'nombreLocalidad');

                    if (localidadInput) {
                        localidadInput.placeholder = 'Buscar localidad...';
                        localidadInput.removeAttribute('readonly');
                    }
                });
        });
    }

    /* =========================================================
       LUGAR DE NACIMIENTO
    ========================================================= */
    const paisSelect   = document.getElementById('paisNacimiento');
    const nacEntidad   = document.getElementById('entidadNacimientoSelect');
    const nacMunicipio = document.getElementById('municipioNacimientoSelect');
    const nacLocalidad = document.getElementById('localidadNacimientoSelect');

    const bloqueSelect = document.getElementById('bloque-select-nacimiento');
    const bloqueInput  = document.getElementById('bloque-input-nacimiento');
    const inputsManual = bloqueInput ? bloqueInput.querySelectorAll('input') : [];

    const nacMunicipioInput = getWrapperInput(nacMunicipio);
    const nacLocalidadInput = getWrapperInput(nacLocalidad);

    const paisNormalizado = () => {
        const opt = paisSelect?.options[paisSelect.selectedIndex];
        return opt?.dataset?.normalizado?.toUpperCase() || '';
    };

    const setModoNacimiento = (modo) => {
        // modo: NONE | MEXICO | EXTRANJERO

        // 1) Apagar ambos visualmente
        bloqueSelect.classList.remove('activo');
        bloqueInput.classList.remove('activo');

        // 2) Reset/habilitación de selects/manuales según modo
        if (modo === 'NONE') {
            // Mostrar ninguno
            // Bloquear selects
            nacEntidad.disabled   = true;
            nacMunicipio.disabled = true;
            nacLocalidad.disabled = true;

            // Reset selects
            resetSelect(nacMunicipio, 'Seleccionar municipio', true);
            resetSelect(nacLocalidad, 'Seleccionar localidad', true);

            // Bloquear manuales + limpiar
            inputsManual.forEach(i => {
                i.disabled = true;
                i.value = '';
            });

            return;
        }

        if (modo === 'MEXICO') {
            // Mostrar SOLO selects
            bloqueSelect.classList.add('activo');

            nacEntidad.disabled   = false;
            nacMunicipio.disabled = true;
            nacLocalidad.disabled = true;

            resetSelect(nacMunicipio, 'Seleccionar municipio', true);
            resetSelect(nacLocalidad, 'Seleccionar localidad', true);

            // Manuales apagados
            inputsManual.forEach(i => {
                i.disabled = true;
                i.value = '';
            });

            return;
        }

        if (modo === 'EXTRANJERO') {
            // Mostrar SOLO manuales
            bloqueInput.classList.add('activo');

            // Apagar selects + limpiar valores
            nacEntidad.value = '';
            nacEntidad.disabled   = true;
            nacMunicipio.disabled = true;
            nacLocalidad.disabled = true;

            resetSelect(nacMunicipio, 'Seleccionar municipio', true);
            resetSelect(nacLocalidad, 'Seleccionar localidad', true);

            // Manuales activos
            inputsManual.forEach(i => {
                i.disabled = false;
            });

            return;
        }
    };

    // Estado inicial
    if (!paisSelect || !paisSelect.value) {
        setModoNacimiento('NONE');
    } else {
        setModoNacimiento(paisNormalizado() === 'MEXICO' ? 'MEXICO' : 'EXTRANJERO');
    }

    // Cambio de país
    paisSelect?.addEventListener('change', () => {
        if (!paisSelect.value) {
            setModoNacimiento('NONE');
            return;
        }
        setModoNacimiento(paisNormalizado() === 'MEXICO' ? 'MEXICO' : 'EXTRANJERO');
    });

    // Entidad (nacimiento) → municipios
    nacEntidad?.addEventListener('change', () => {
        if (paisNormalizado() !== 'MEXICO') return;

        const idEntidad = nacEntidad.value;

        // Cambiar texto de option inicial, según país ya seleccionado
        // (aquí el país ya está seleccionado, así que debe ser “Seleccionar”)
        setFirstOptionText(nacEntidad, 'Seleccionar');

        resetSelect(nacMunicipio, 'Cargando...', true);
        resetSelect(nacLocalidad, 'Seleccionar', true);

        if (nacMunicipioInput) {
            nacMunicipioInput.value = '';
            nacMunicipioInput.placeholder = idEntidad ? 'Buscar municipio...' : 'Seleccione entidad';
            if (!idEntidad) nacMunicipioInput.setAttribute('readonly', 'readonly');
        }

        if (nacLocalidadInput) {
            nacLocalidadInput.value = '';
            nacLocalidadInput.placeholder = 'Seleccione municipio';
            nacLocalidadInput.setAttribute('readonly', 'readonly');
        }

        if (!idEntidad) return;

        fetch(`/api/municipios/${idEntidad}`)
            .then(r => r.json())
            .then(data => {
                fillSelect(nacMunicipio, 'Seleccionar', data, 'idMunicipio', 'nombreMunicipio');

                if (nacMunicipioInput) {
                    nacMunicipioInput.placeholder = 'Buscar municipio...';
                    nacMunicipioInput.removeAttribute('readonly');
                }
            });
    });

    // Municipio (nacimiento) → localidades
    nacMunicipio?.addEventListener('change', () => {
        if (paisNormalizado() !== 'MEXICO') return;

        const idMunicipio = nacMunicipio.value;

        resetSelect(nacLocalidad, 'Cargando...', true);

        if (nacLocalidadInput) {
            nacLocalidadInput.value = '';
            nacLocalidadInput.placeholder = idMunicipio ? 'Buscar localidad...' : 'Seleccione municipio';
            if (!idMunicipio) nacLocalidadInput.setAttribute('readonly', 'readonly');
        }

        if (!idMunicipio) return;

        fetch(`/api/localidades/${idMunicipio}`)
            .then(r => r.json())
            .then(data => {
                fillSelect(nacLocalidad, 'Seleccionar', data, 'idLocalidad', 'nombreLocalidad');

                if (nacLocalidadInput) {
                    nacLocalidadInput.placeholder = 'Buscar localidad...';
                    nacLocalidadInput.removeAttribute('readonly');
                }
            });
    });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    function initSelectBuscable(wrapper) {
        const input  = wrapper.querySelector('.select-buscable-input');
        const list   = wrapper.querySelector('.select-buscable-list');
        const select = wrapper.querySelector('select');

        if (!input || !list || !select) return;

        /* =====================================================
           SINCRONIZAR ESTADO
           - select.disabled  → input.readonly
        ===================================================== */
        function syncDisabled() {
            if (select.disabled) {
                input.setAttribute('readonly', 'readonly');
                input.value = '';
                list.style.display = 'none';
            } else {
                input.removeAttribute('readonly');
            }
        }

        // Estado inicial
        syncDisabled();

        /* =====================================================
           INPUT → FILTRADO
        ===================================================== */
        input.addEventListener('input', function () {

            if (input.hasAttribute('readonly')) return;

            const term = this.value.toLowerCase().trim();
            list.innerHTML = '';

            if (!term) {
                list.style.display = 'none';
                return;
            }

            [...select.options].forEach(opt => {
                if (!opt.value) return;

                const text = opt.textContent.trim();

                if (text.toLowerCase().includes(term)) {
                    const li = document.createElement('li');
                    li.textContent = text;

                    li.addEventListener('click', () => {
                        select.value = opt.value;
                        input.value  = text;
                        list.style.display = 'none';

                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    });

                    list.appendChild(li);
                }
            });

            list.style.display = list.children.length ? 'block' : 'none';
        });

        /* =====================================================
           CLICK FUERA → CERRAR LISTA
        ===================================================== */
        document.addEventListener('click', function (e) {
            if (!wrapper.contains(e.target)) {
                list.style.display = 'none';
            }
        });

        /* =====================================================
           OBSERVAR CAMBIOS EN disabled DEL SELECT
        ===================================================== */
        const observer = new MutationObserver(syncDisabled);
        observer.observe(select, {
            attributes: true,
            attributeFilter: ['disabled']
        });
    }

    document
        .querySelectorAll('.select-buscable-wrapper')
        .forEach(initSelectBuscable);

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const horariosContainer = document.getElementById('horarios-container');
    const addHorarioBtn = document.getElementById('agregar-horario');
    const template = document.getElementById('horario-template');

    if (!horariosContainer || !addHorarioBtn || !template) {
        return;
    }

    const getRowKey = (row) => {
        const diaSelect = row.querySelector('select[name*="[idDiaSemana]"]');
        const rangoSelect = row.querySelector('.rango-select');
        const horaInicio = row.querySelector('input[name*="[horaInicio]"]');
        const horaFin = row.querySelector('input[name*="[horaFin]"]');

        const idDia = diaSelect ? diaSelect.value : '';
        const rangoValue = rangoSelect ? rangoSelect.value : '';

        if (!idDia || !rangoValue) {
            return null;
        }

        if (rangoValue === 'manual') {
            const inicio = horaInicio ? horaInicio.value : '';
            const fin = horaFin ? horaFin.value : '';
            if (!inicio || !fin) {
                return null;
            }
            return `${idDia}|manual|${inicio}-${fin}`;
        }

        return `${idDia}|${rangoValue}`;
    };

    const isDuplicate = (row) => {
        const key = getRowKey(row);
        if (!key) {
            return false;
        }

        const rows = horariosContainer.querySelectorAll('.horario-row');
        for (const other of rows) {
            if (other === row) {
                continue;
            }
            if (getRowKey(other) === key) {
                return true;
            }
        }
        return false;
    };

    const resetRowSelection = (row) => {
        const diaSelect = row.querySelector('select[name*=\"[idDiaSemana]\"]');
        const rangoSelect = row.querySelector('.rango-select');
        const horaInicio = row.querySelector('input[name*=\"[horaInicio]\"]');
        const horaFin = row.querySelector('input[name*=\"[horaFin]\"]');
        const manualFields = row.querySelectorAll('.horario-manual');

        if (rangoSelect) {
            rangoSelect.value = '';
        }
        if (horaInicio) {
            horaInicio.value = '';
        }
        if (horaFin) {
            horaFin.value = '';
        }
        manualFields.forEach(field => {
            field.style.display = 'none';
            const input = field.querySelector('input');
            if (input) {
                input.required = false;
            }
        });
        if (diaSelect) {
            diaSelect.value = '';
        }
    };

    const bindHorarioRow = (row) => {
        const diaSelect = row.querySelector('select[name*="[idDiaSemana]"]');
        const rangoSelect = row.querySelector('.rango-select');
        const manualFields = row.querySelectorAll('.horario-manual');
        const horaInicio = row.querySelector('input[name*="[horaInicio]"]');
        const horaFin = row.querySelector('input[name*="[horaFin]"]');

        const toggleManual = () => {
            const isManual = rangoSelect && rangoSelect.value === 'manual';

            manualFields.forEach(field => {
                field.style.display = isManual ? '' : 'none';
                const input = field.querySelector('input');
                if (input) {
                    input.required = isManual;
                    if (!isManual) {
                        input.value = '';
                    }
                }
            });
        };

        if (rangoSelect) {
            rangoSelect.addEventListener('change', toggleManual);
            rangoSelect.addEventListener('change', () => {
                if (isDuplicate(row)) {
                    alert('Ya existe la misma combinacion de dia y rango de horario.');
                    resetRowSelection(row);
                }
            });
        }

        toggleManual();

        if (diaSelect) {
            diaSelect.addEventListener('change', () => {
                if (isDuplicate(row)) {
                    alert('Ya existe la misma combinacion de dia y rango de horario.');
                    resetRowSelection(row);
                }
            });
        }

        if (horaInicio) {
            horaInicio.addEventListener('change', () => {
                if (isDuplicate(row)) {
                    alert('Ya existe la misma combinacion de dia y rango de horario.');
                    resetRowSelection(row);
                }
            });
        }

        if (horaFin) {
            horaFin.addEventListener('change', () => {
                if (isDuplicate(row)) {
                    alert('Ya existe la misma combinacion de dia y rango de horario.');
                    resetRowSelection(row);
                }
            });
        }

        const removeBtn = row.querySelector('.btn-quitar-horario');
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                row.remove();
            });
        }
    };

    horariosContainer.querySelectorAll('.horario-row').forEach(bindHorarioRow);

    let nextIndex = horariosContainer.querySelectorAll('.horario-row').length;

    addHorarioBtn.addEventListener('click', () => {
        const html = template.innerHTML.replace(/__INDEX__/g, nextIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        const newRow = wrapper.firstElementChild;

        if (!newRow) {
            return;
        }

        horariosContainer.appendChild(newRow);
        bindHorarioRow(newRow);
        nextIndex += 1;
    });
});
</script>

</body>
</html>
