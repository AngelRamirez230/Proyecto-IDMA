<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuario</title>
    @vite(['resources/css/app.css'])
</head>

<body>
@include('layouts.barraNavegacion')

<main class="form-container">

    <form action="{{ route('usuarios.update', $usuario->idUsuario) }}" method="POST" class="formulario">
        @csrf
        @method('PUT')

        <h1 class="titulo-form">
            Editar usuario - Rol:
            <strong>{{ $usuario->tipoDeUsuario->nombreTipoDeUsuario ?? 'N/D' }}</strong>
        </h1>

        <h3 class="subtitulo-form">Datos personales</h3>

        {{-- PRIMER NOMBRE --}}
        <div class="form-group">
            <label for="primer_nombre">Primer nombre:</label>
            <input type="text" id="primer_nombre" name="primer_nombre" class="input-mediano"
                   value="{{ old('primer_nombre', $usuario->primerNombre) }}" required>
            <x-error-field field="primer_nombre" />
        </div>

        {{-- SEGUNDO NOMBRE --}}
        <div class="form-group">
            <label for="segundo_nombre">Segundo nombre:</label>
            <input type="text" id="segundo_nombre" name="segundo_nombre" class="input-mediano"
                   value="{{ old('segundo_nombre', $usuario->segundoNombre) }}">
            <x-error-field field="segundo_nombre" />
        </div>

        {{-- PRIMER APELLIDO --}}
        <div class="form-group">
            <label for="primer_apellido">Primer apellido:</label>
            <input type="text" id="primer_apellido" name="primer_apellido" class="input-mediano"
                   value="{{ old('primer_apellido', $usuario->primerApellido) }}" required>
            <x-error-field field="primer_apellido" />
        </div>

        {{-- SEGUNDO APELLIDO --}}
        <div class="form-group">
            <label for="segundo_apellido">Segundo apellido:</label>
            <input type="text" id="segundo_apellido" name="segundo_apellido" class="input-mediano"
                   value="{{ old('segundo_apellido', $usuario->segundoApellido) }}">
            <x-error-field field="segundo_apellido" />
        </div>

        {{-- SEXO --}}
        <div class="form-group">
            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" class="select" required>
                <option value="" disabled {{ old('sexo', $usuario->idSexo) ? '' : 'selected' }}>Seleccionar</option>
                @foreach($sexos as $sx)
                    <option value="{{ $sx->idSexo }}" {{ (string)old('sexo', $usuario->idSexo) === (string)$sx->idSexo ? 'selected' : '' }}>
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
                <option value="" disabled {{ old('estadoCivil', $usuario->idEstadoCivil) ? '' : 'selected' }}>Seleccionar</option>
                @foreach($estadosCiviles as $ec)
                    <option value="{{ $ec->idEstadoCivil }}" {{ (string)old('estadoCivil', $usuario->idEstadoCivil) === (string)$ec->idEstadoCivil ? 'selected' : '' }}>
                        {{ $ec->nombreEstadoCivil }}
                    </option>
                @endforeach
            </select>
            <x-error-field field="estadoCivil" />
        </div>

        {{-- TELÉFONO --}}
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" class="input-chico"
                   value="{{ old('telefono', $usuario->telefono) }}">
            <x-error-field field="telefono" />
        </div>

        {{-- TELÉFONO FIJO --}}
        <div class="form-group">
            <label for="telefonoFijo">Teléfono fijo:</label>
            <input type="text" id="telefonoFijo" name="telefonoFijo" class="input-chico"
                   value="{{ old('telefonoFijo', $usuario->telefonoFijo) }}">
            <x-error-field field="telefonoFijo" />
        </div>

        {{-- CORREO INSTITUCIONAL --}}
        <div class="form-group">
            <label for="emailInstitucional">Correo institucional:</label>
            <input type="email" id="emailInstitucional" name="emailInstitucional" class="input-mediano"
                   value="{{ old('emailInstitucional', $usuario->correoInstitucional) }}">
            <x-error-field field="emailInstitucional" />
        </div>

        {{-- PASSWORD (OPCIONAL) --}}
        <div class="form-group">
            <label for="password">Contraseña (opcional):</label>
            <input type="password" id="password" name="password" class="input-chico"
                   placeholder="Deja vacío para conservar la actual">
            <x-error-field field="password" />
        </div>

        {{-- NOMBRE USUARIO --}}
        <div class="form-group">
            <label for="nombreUsuario">Nombre de usuario:</label>
            <input type="text" id="nombreUsuario" name="nombreUsuario" class="input-mediano"
                   value="{{ old('nombreUsuario', $usuario->nombreUsuario) }}" required>
            <x-error-field field="nombreUsuario" />
        </div>

        {{-- FECHA NACIMIENTO --}}
        <div class="form-group">
            <label for="fechaNacimiento">Fecha de nacimiento:</label>
            <input type="date" id="fechaNacimiento" name="fechaNacimiento" max="{{ date('Y-m-d') }}"
                   class="input-date"
                   value="{{ old('fechaNacimiento', $usuario->fechaDeNacimiento) }}">
            <x-error-field field="fechaNacimiento" />
        </div>

        {{-- CURP --}}
        <div class="form-group">
            <label for="curp">CURP:</label>
            <input type="text" id="curp" name="curp" class="input-chico"
                   value="{{ old('curp', $usuario->CURP) }}">
            <x-error-field field="curp" />
        </div>

        {{-- RFC --}}
        <div class="form-group">
            <label for="rfc">RFC:</label>
            <input type="text" id="rfc" name="rfc" class="input-chico"
                   value="{{ old('rfc', $usuario->RFC) }}">
            <x-error-field field="rfc" />
        </div>

        {{-- CORREO PERSONAL --}}
        <div class="form-group">
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" class="input-mediano"
                   value="{{ old('email', $usuario->correoElectronico) }}">
            <x-error-field field="email" />
        </div>

        <h3 class="subtitulo-form">Datos del domicilio</h3>

        {{-- ENTIDAD --}}
        <div class="form-group">
            <label>Entidad:</label>
            <select id="entidad" name="entidad" class="select select-buscable">
                <option value="">Seleccionar</option>
                @foreach($entidades as $e)
                    <option value="{{ $e->idEntidad }}"
                        {{ (string)old('entidad', $domEntidadId ?? '') === (string)$e->idEntidad ? 'selected' : '' }}>
                        {{ $e->nombreEntidad }}
                    </option>
                @endforeach
            </select>
            <x-error-field field="entidad" />
        </div>

        {{-- MUNICIPIO (BUSCABLE) --}}
        <div class="form-group">
            <label>Municipio:</label>
            <div class="select-buscable-wrapper">
                <input type="text" class="input-mediano select-buscable-input"
                       placeholder="Seleccione entidad"
                       data-target="municipio"
                       autocomplete="off" readonly>
                <ul class="select-buscable-list"></ul>
                <select id="municipio" name="municipio" hidden disabled>
                    <option value="">Seleccionar</option>
                </select>
            </div>
            <x-error-field field="municipio" />
        </div>

        {{-- LOCALIDAD (BUSCABLE) --}}
        <div class="form-group">
            <label>Localidad:</label>
            <div class="select-buscable-wrapper">
                <input type="text" class="input-mediano select-buscable-input"
                       placeholder="Seleccione municipio"
                       data-target="localidad"
                       autocomplete="off" readonly>
                <ul class="select-buscable-list"></ul>
                <select id="localidad" name="localidad" hidden disabled>
                    <option value="">Seleccionar</option>
                </select>
            </div>
            <x-error-field field="localidad" />
        </div>

        {{-- LOCALIDAD MANUAL --}}
        <div class="form-group" id="localidadManualDomicilio" style="display:none;">
            <label for="localidadManual">Localidad (manual):</label>
            <input type="text" id="localidadManual" name="localidadManual" class="input-mediano"
                   value="{{ old('localidadManual') }}">
            <x-error-field field="localidadManual" />
        </div>

        {{-- COLONIA --}}
        <div class="form-group">
            <label for="colonia">Colonia:</label>
            <input type="text" id="colonia" name="colonia" class="input-mediano"
                   value="{{ old('colonia', optional($usuario->domicilio)->colonia) }}">
            <x-error-field field="colonia" />
        </div>

        {{-- CP --}}
        <div class="form-group">
            <label for="codigoPostal">Código postal:</label>
            <input type="text" id="codigoPostal" name="codigoPostal" class="input-chico"
                   value="{{ old('codigoPostal', optional($usuario->domicilio)->codigoPostal) }}">
            <x-error-field field="codigoPostal" />
        </div>

        {{-- CALLE --}}
        <div class="form-group">
            <label for="calle">Calle:</label>
            <input type="text" id="calle" name="calle" class="input-grande"
                   value="{{ old('calle', optional($usuario->domicilio)->calle) }}">
            <x-error-field field="calle" />
        </div>

        {{-- NÚMERO EXTERIOR --}}
        <div class="form-group">
            <label for="numeroExterior">Número exterior:</label>
            <input type="text" id="numeroExterior" name="numeroExterior" class="input-chico"
                   value="{{ old('numeroExterior', optional($usuario->domicilio)->numeroExterior) }}">
            <x-error-field field="numeroExterior" />
        </div>

        {{-- NÚMERO INTERIOR --}}
        <div class="form-group">
            <label for="numeroInterior">Número interior:</label>
            <input type="text" id="numeroInterior" name="numeroInterior" class="input-chico"
                   value="{{ old('numeroInterior', optional($usuario->domicilio)->numeroInterior) }}">
            <x-error-field field="numeroInterior" />
        </div>

        <h3 class="subtitulo-form">Lugar de nacimiento</h3>

        {{-- PAÍS NACIMIENTO --}}
        <div class="form-group">
            <label for="paisNacimiento">País:</label>
            <select id="paisNacimiento" name="paisNacimiento" class="select select-buscable" required>
                <option value="">Seleccionar</option>
                @foreach($paises as $pais)
                    <option value="{{ $pais->idPais }}"
                            data-normalizado="{{ $pais->nombrePaisNormalizado }}"
                            {{ (string)old('paisNacimiento', $paisNacimientoId ?? '') === (string)$pais->idPais ? 'selected' : '' }}>
                        {{ $pais->nombrePais }}
                    </option>
                @endforeach
            </select>
            <x-error-field field="paisNacimiento" />
        </div>

        <div id="bloque-select-nacimiento">
            {{-- ENTIDAD NACIMIENTO --}}
            <div class="form-group">
                <label>Entidad de nacimiento:</label>
                <select id="entidadNacimientoSelect" name="entidadNacimiento" class="select select-buscable">
                    <option value="">Seleccionar país</option>
                    @foreach($entidades as $e)
                        <option value="{{ $e->idEntidad }}"
                            {{ (string)old('entidadNacimiento', $nacEntidadId ?? '') === (string)$e->idEntidad ? 'selected' : '' }}>
                            {{ $e->nombreEntidad }}
                        </option>
                    @endforeach
                </select>
                <x-error-field field="entidadNacimiento" />
            </div>

            {{-- MUNICIPIO NACIMIENTO (BUSCABLE) --}}
            <div class="form-group">
                <label>Municipio de nacimiento:</label>
                <div class="select-buscable-wrapper">
                    <input type="text" class="input-mediano select-buscable-input"
                           placeholder="Seleccione entidad" data-target="municipioNacimientoSelect"
                           autocomplete="off" readonly>
                    <ul class="select-buscable-list"></ul>
                    <select id="municipioNacimientoSelect" name="municipioNacimiento" hidden disabled>
                        <option value="">Seleccionar</option>
                    </select>
                </div>
                <x-error-field field="municipioNacimiento" />
            </div>

            {{-- LOCALIDAD NACIMIENTO (BUSCABLE) --}}
            <div class="form-group">
                <label>Localidad de nacimiento:</label>
                <div class="select-buscable-wrapper">
                    <input type="text" class="input-mediano select-buscable-input"
                           placeholder="Seleccione municipio" data-target="localidadNacimientoSelect"
                           autocomplete="off" readonly>
                    <ul class="select-buscable-list"></ul>
                    <select id="localidadNacimientoSelect" name="localidadNacimiento" hidden disabled>
                        <option value="">Seleccionar</option>
                    </select>
                </div>
                <x-error-field field="localidadNacimiento" />
            </div>
        </div>

        <div id="bloque-input-nacimiento" style="display:none;">
            <div class="form-group">
                <label>Entidad de nacimiento:</label>
                <input type="text" name="entidadNacimientoManual" class="input-mediano"
                       value="{{ old('entidadNacimientoManual') }}" disabled>
                <x-error-field field="entidadNacimientoManual" />
            </div>

            <div class="form-group">
                <label>Municipio de nacimiento:</label>
                <input type="text" name="municipioNacimientoManual" class="input-mediano"
                       value="{{ old('municipioNacimientoManual') }}" disabled>
                <x-error-field field="municipioNacimientoManual" />
            </div>

            <div class="form-group">
                <label>Localidad de nacimiento:</label>
                <input type="text" name="localidadNacimientoManual" class="input-mediano"
                       value="{{ old('localidadNacimientoManual') }}" disabled>
                <x-error-field field="localidadNacimientoManual" />
            </div>
        </div>

        {{-- BOTONES --}}
        <div class="form-group form-botones">
            <button type="submit" class="btn-boton-formulario">Guardar cambios</button>
            <a href="{{ route('consultaUsuarios') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>

    </form>

    {{-- ERRORES --}}
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

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {

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

    const getWrapperInput = (selectEl) => {
        return selectEl?.closest('.select-buscable-wrapper')?.querySelector('.select-buscable-input') || null;
    };

    const setFirstOptionText = (select, text) => {
        if (!select || !select.options || select.options.length === 0) return;
        select.options[0].textContent = text;
    };

    // =========================
    // DOMICILIO: precarga
    // =========================
    const domEntidad   = document.getElementById('entidad');
    const domMunicipio = document.getElementById('municipio');
    const domLocalidad = document.getElementById('localidad');

    const domMunicipioInput = getWrapperInput(domMunicipio);
    const domLocalidadInput = getWrapperInput(domLocalidad);

    const domEntidadId   = @json(old('entidad', $domEntidadId ?? null));
    const domMunicipioId = @json(old('municipio', $domMunicipioId ?? null));
    const domLocalidadId = @json(old('localidad', $domLocalidadId ?? null));

    if (domMunicipioInput) domMunicipioInput.placeholder = domEntidadId ? 'Buscar municipio...' : 'Seleccione entidad';
    if (domLocalidadInput) domLocalidadInput.placeholder = domMunicipioId ? 'Buscar localidad...' : 'Seleccione municipio';

    const precargarDomicilio = async () => {
        if (!domEntidad || !domEntidadId) return;

        // municipios
        resetSelect(domMunicipio, 'Cargando...', true);
        const munData = await fetch(`/api/municipios/${domEntidadId}`).then(r => r.json());
        fillSelect(domMunicipio, 'Seleccionar', munData, 'idMunicipio', 'nombreMunicipio');
        domMunicipio.value = domMunicipioId ?? '';

        if (domMunicipioInput) {
            domMunicipioInput.removeAttribute('readonly');
            domMunicipioInput.value = domMunicipio.options[domMunicipio.selectedIndex]?.textContent?.trim() || '';
            domMunicipioInput.placeholder = 'Buscar municipio...';
        }

        // localidades
        if (!domMunicipio.value) return;
        resetSelect(domLocalidad, 'Cargando...', true);
        const locData = await fetch(`/api/localidades/${domMunicipio.value}`).then(r => r.json());
        fillSelect(domLocalidad, 'Seleccionar', locData, 'idLocalidad', 'nombreLocalidad');
        domLocalidad.value = domLocalidadId ?? '';

        if (domLocalidadInput) {
            domLocalidadInput.removeAttribute('readonly');
            domLocalidadInput.value = domLocalidad.options[domLocalidad.selectedIndex]?.textContent?.trim() || '';
            domLocalidadInput.placeholder = 'Buscar localidad...';
        }
    };

    // eventos cascada domicilio (igual que alta)
    if (domEntidad && domMunicipio && domLocalidad) {
        domEntidad.addEventListener('change', () => {
            const idEntidad = domEntidad.value;

            resetSelect(domMunicipio, 'Seleccionar', true);
            resetSelect(domLocalidad, 'Seleccionar', true);

            if (domMunicipioInput) {
                domMunicipioInput.value = '';
                domMunicipioInput.placeholder = idEntidad ? 'Buscar municipio...' : 'Seleccione entidad';
                if (!idEntidad) domMunicipioInput.setAttribute('readonly','readonly');
            }

            if (domLocalidadInput) {
                domLocalidadInput.value = '';
                domLocalidadInput.placeholder = 'Seleccione municipio';
                domLocalidadInput.setAttribute('readonly','readonly');
            }

            if (!idEntidad) return;

            fetch(`/api/municipios/${idEntidad}`)
                .then(r => r.json())
                .then(data => {
                    fillSelect(domMunicipio, 'Seleccionar', data, 'idMunicipio', 'nombreMunicipio');
                    if (domMunicipioInput) domMunicipioInput.removeAttribute('readonly');
                });
        });

        domMunicipio.addEventListener('change', () => {
            const idMunicipio = domMunicipio.value;

            resetSelect(domLocalidad, 'Seleccionar', true);

            if (domLocalidadInput) {
                domLocalidadInput.value = '';
                domLocalidadInput.placeholder = idMunicipio ? 'Buscar localidad...' : 'Seleccione municipio';
                if (!idMunicipio) domLocalidadInput.setAttribute('readonly','readonly');
            }

            if (!idMunicipio) return;

            fetch(`/api/localidades/${idMunicipio}`)
                .then(r => r.json())
                .then(data => {
                    fillSelect(domLocalidad, 'Seleccionar', data, 'idLocalidad', 'nombreLocalidad');
                    if (domLocalidadInput) domLocalidadInput.removeAttribute('readonly');
                });
        });
    }

    // =========================
    // NACIMIENTO: precarga
    // =========================
    const paisSelect   = document.getElementById('paisNacimiento');
    const nacEntidad   = document.getElementById('entidadNacimientoSelect');
    const nacMunicipio = document.getElementById('municipioNacimientoSelect');
    const nacLocalidad = document.getElementById('localidadNacimientoSelect');

    const bloqueSelect = document.getElementById('bloque-select-nacimiento');
    const bloqueInput  = document.getElementById('bloque-input-nacimiento');
    const inputsManual = bloqueInput ? bloqueInput.querySelectorAll('input') : [];

    const nacMunicipioInput = getWrapperInput(nacMunicipio);
    const nacLocalidadInput = getWrapperInput(nacLocalidad);

    const nacEntidadId   = @json(old('entidadNacimiento', $nacEntidadId ?? null));
    const nacMunicipioId = @json(old('municipioNacimiento', $nacMunicipioId ?? null));
    const nacLocalidadId = @json(old('localidadNacimiento', $nacLocalidadId ?? null));

    const paisNormalizado = () => {
        const opt = paisSelect?.options[paisSelect.selectedIndex];
        return opt?.dataset?.normalizado?.toUpperCase() || '';
    };

    const setModoNacimiento = (modo) => {
        // NONE | MEXICO | EXTRANJERO
        bloqueSelect.style.display = (modo === 'MEXICO') ? 'block' : (modo === 'NONE' ? 'block' : 'none');
        bloqueInput.style.display  = (modo === 'EXTRANJERO') ? 'block' : 'none';

        if (modo === 'NONE') {
            nacEntidad.disabled = true;
            nacMunicipio.disabled = true;
            nacLocalidad.disabled = true;
            resetSelect(nacMunicipio, 'Seleccionar municipio', true);
            resetSelect(nacLocalidad, 'Seleccionar localidad', true);
            inputsManual.forEach(i => { i.disabled = true; i.value = ''; });
            return;
        }

        if (modo === 'MEXICO') {
            nacEntidad.disabled = false;
            nacMunicipio.disabled = true;
            nacLocalidad.disabled = true;
            resetSelect(nacMunicipio, 'Seleccionar municipio', true);
            resetSelect(nacLocalidad, 'Seleccionar localidad', true);
            inputsManual.forEach(i => { i.disabled = true; i.value = ''; });
            return;
        }

        if (modo === 'EXTRANJERO') {
            nacEntidad.value = '';
            nacEntidad.disabled = true;
            nacMunicipio.disabled = true;
            nacLocalidad.disabled = true;
            resetSelect(nacMunicipio, 'Seleccionar municipio', true);
            resetSelect(nacLocalidad, 'Seleccionar localidad', true);
            inputsManual.forEach(i => { i.disabled = false; });
            return;
        }
    };

    const precargarNacimiento = async () => {
        if (!paisSelect?.value) { setModoNacimiento('NONE'); return; }

        const esMexico = paisNormalizado() === 'MEXICO';
        setModoNacimiento(esMexico ? 'MEXICO' : 'EXTRANJERO');

        if (!esMexico) return;
        if (!nacEntidadId) return;

        // Asegurar placeholder de entidad
        setFirstOptionText(nacEntidad, 'Seleccionar');

        // Municipios
        resetSelect(nacMunicipio, 'Cargando...', true);
        const munData = await fetch(`/api/municipios/${nacEntidadId}`).then(r => r.json());
        fillSelect(nacMunicipio, 'Seleccionar', munData, 'idMunicipio', 'nombreMunicipio');
        nacMunicipio.value = nacMunicipioId ?? '';

        if (nacMunicipioInput) {
            nacMunicipioInput.removeAttribute('readonly');
            nacMunicipioInput.value = nacMunicipio.options[nacMunicipio.selectedIndex]?.textContent?.trim() || '';
            nacMunicipioInput.placeholder = 'Buscar municipio...';
        }

        // Localidades
        if (!nacMunicipio.value) return;
        resetSelect(nacLocalidad, 'Cargando...', true);
        const locData = await fetch(`/api/localidades/${nacMunicipio.value}`).then(r => r.json());
        fillSelect(nacLocalidad, 'Seleccionar', locData, 'idLocalidad', 'nombreLocalidad');
        nacLocalidad.value = nacLocalidadId ?? '';

        if (nacLocalidadInput) {
            nacLocalidadInput.removeAttribute('readonly');
            nacLocalidadInput.value = nacLocalidad.options[nacLocalidad.selectedIndex]?.textContent?.trim() || '';
            nacLocalidadInput.placeholder = 'Buscar localidad...';
        }
    };

    // listeners nacimiento (igual que alta)
    paisSelect?.addEventListener('change', precargarNacimiento);

    nacEntidad?.addEventListener('change', () => {
        if (paisNormalizado() !== 'MEXICO') return;

        const idEntidad = nacEntidad.value;
        setFirstOptionText(nacEntidad, 'Seleccionar');

        resetSelect(nacMunicipio, 'Cargando...', true);
        resetSelect(nacLocalidad, 'Seleccionar', true);

        if (nacMunicipioInput) {
            nacMunicipioInput.value = '';
            nacMunicipioInput.placeholder = idEntidad ? 'Buscar municipio...' : 'Seleccione entidad';
            if (!idEntidad) nacMunicipioInput.setAttribute('readonly','readonly');
        }

        if (!idEntidad) return;

        fetch(`/api/municipios/${idEntidad}`)
            .then(r => r.json())
            .then(data => {
                fillSelect(nacMunicipio, 'Seleccionar', data, 'idMunicipio', 'nombreMunicipio');
                if (nacMunicipioInput) nacMunicipioInput.removeAttribute('readonly');
            });
    });

    nacMunicipio?.addEventListener('change', () => {
        if (paisNormalizado() !== 'MEXICO') return;

        const idMunicipio = nacMunicipio.value;
        resetSelect(nacLocalidad, 'Cargando...', true);

        if (nacLocalidadInput) {
            nacLocalidadInput.value = '';
            nacLocalidadInput.placeholder = idMunicipio ? 'Buscar localidad...' : 'Seleccione municipio';
            if (!idMunicipio) nacLocalidadInput.setAttribute('readonly','readonly');
        }

        if (!idMunicipio) return;

        fetch(`/api/localidades/${idMunicipio}`)
            .then(r => r.json())
            .then(data => {
                fillSelect(nacLocalidad, 'Seleccionar', data, 'idLocalidad', 'nombreLocalidad');
                if (nacLocalidadInput) nacLocalidadInput.removeAttribute('readonly');
            });
    });

    // =========================
    // Init buscables (tu componente)
    // =========================
    function initSelectBuscable(wrapper) {
        const input  = wrapper.querySelector('.select-buscable-input');
        const list   = wrapper.querySelector('.select-buscable-list');
        const select = wrapper.querySelector('select');
        if (!input || !list || !select) return;

        function syncDisabled() {
            if (select.disabled) {
                input.setAttribute('readonly', 'readonly');
                input.value = '';
                list.style.display = 'none';
            } else {
                input.removeAttribute('readonly');
            }
        }

        syncDisabled();

        input.addEventListener('input', function () {
            if (input.hasAttribute('readonly')) return;

            const term = this.value.toLowerCase().trim();
            list.innerHTML = '';

            if (!term) { list.style.display = 'none'; return; }

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

        document.addEventListener('click', function (e) {
            if (!wrapper.contains(e.target)) list.style.display = 'none';
        });

        const observer = new MutationObserver(syncDisabled);
        observer.observe(select, { attributes: true, attributeFilter: ['disabled'] });
    }

    document.querySelectorAll('.select-buscable-wrapper').forEach(initSelectBuscable);

    // Ejecutar precargas
    precargarDomicilio();
    precargarNacimiento();
});
</script>

</body>
</html>
