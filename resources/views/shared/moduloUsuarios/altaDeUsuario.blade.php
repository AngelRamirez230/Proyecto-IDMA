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
        </div>

        {{-- SEXO --}}
        <div class="form-group">
            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" class="select" required>
                <option value="" disabled {{ old('sexo') ? '' : 'selected' }}>Seleccionar</option>
                @foreach($sexos as $sx)
                    <option
                        value="{{ $sx->idSexo }}"
                        {{ old('sexo') == $sx->idSexo ? 'selected' : '' }}
                    >
                        {{ $sx->nombreSexo }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="estadoCivil">Estado civil:</label>
            <select id="estadoCivil" name="estadoCivil" class="select" required>
                <option value="" disabled {{ old('estadoCivil') ? '' : 'selected' }}>Seleccionar</option>
                @foreach($estadosCiviles as $ec)
                    <option
                        value="{{ $ec->idEstadoCivil }}"
                        {{ old('estadoCivil') == $ec->idEstadoCivil ? 'selected' : '' }}
                    >
                        {{ $ec-> nombreEstadoCivil }}
                    </option>
                @endforeach
            </select>
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
        </div>

        <h3 class="subtitulo-form">Domicilio</h3>

        {{-- ENTIDAD --}}
            <div class="form-group">
                <label>Entidad:</label>
                <select id="entidad" name="entidad" class="select select-buscable">
                    <option value="">Seleccionar</option>
                    @foreach($entidades as $e)
                        <option value="{{ $e->idEntidad }}">{{ $e->nombreEntidad }}</option>
                    @endforeach
                </select>
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
        </div>

        <div id="bloque-select-nacimiento">
            {{-- ENTIDAD --}}
            <div class="form-group">
                <label>Entidad de nacimiento:</label>
                <select id="entidadNacimientoSelect" name="entidadNacimiento" class="select select-buscable">
                    <option value="">Seleccionar</option>
                    @foreach($entidades as $e)
                        <option value="{{ $e->idEntidad }}">{{ $e->nombreEntidad }}</option>
                    @endforeach
                </select>
            </div>

            {{-- MUNICIPIO --}}
            <div class="form-group">
                <label>Municipio de nacimiento:</label>
                <select id="municipioNacimientoSelect" name="municipioNacimiento" class="select select-buscable">
                    <option value="">Seleccionar</option>
                </select>
            </div>

            {{-- LOCALIDAD --}}
            <div class="form-group">
                <label>Localidad de nacimiento:</label>
                <select id="localidadNacimientoSelect" name="localidadNacimiento" class="select select-buscable">
                    <option value="">Seleccionar</option>
                </select>
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
                    disabled
                >
            </div>

            <div class="form-group">
                <label>Municipio de nacimiento:</label>
                <input
                    type="text"
                    name="municipioNacimientoManual"
                    class="input-mediano"
                    placeholder="Escribe el municipio"
                    disabled
                >
            </div>

            <div class="form-group">
                <label>Localidad de nacimiento:</label>
                <input
                    type="text"
                    name="localidadNacimientoManual"
                    class="input-mediano"
                    placeholder="Escribe la localidad"
                    disabled
                >
            </div>
        </div>

        {{-- BOTONES --}}
        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Guardar</button>
            <a href="{{ route('apartadoUsuarios') }}" class="btn-boton-formulario btn-cancelar">
                Cancelar
            </a>
        </div>

    </form>
</main>


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

    /* =========================================================
   DOMICILIO
   entidad → municipio → localidad
========================================================= */
const domEntidad   = document.getElementById('entidad');
const domMunicipio = document.getElementById('municipio');
const domLocalidad = document.getElementById('localidad');

if (domEntidad && domMunicipio && domLocalidad) {

    // Referencias a los inputs buscables
    const municipioInput = domMunicipio
        .closest('.select-buscable-wrapper')
        ?.querySelector('.select-buscable-input');

    const localidadInput = domLocalidad
        .closest('.select-buscable-wrapper')
        ?.querySelector('.select-buscable-input');

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

        // Reset municipio y localidad
        resetSelect(domMunicipio, 'Seleccionar', true);
        resetSelect(domLocalidad, 'Selecciona un municipio', true);

        if (municipioInput) {
            municipioInput.value = '';
            municipioInput.placeholder = idEntidad
                ? 'Buscar municipio...'
                : 'Seleccione entidad';
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
                fillSelect(
                    domMunicipio,
                    'Seleccionar',
                    data,
                    'idMunicipio',
                    'nombreMunicipio',
                    false
                );

                // Habilitar input buscable de municipio
                if (municipioInput) {
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
            localidadInput.placeholder = idMunicipio
                ? 'Buscar localidad...'
                : 'Seleccione municipio';
        }

        if (!idMunicipio) return;

        fetch(`/api/localidades/${idMunicipio}`)
            .then(r => r.json())
            .then(data => {
                fillSelect(
                    domLocalidad,
                    'Seleccionar',
                    data,
                    'idLocalidad',
                    'nombreLocalidad',
                    false
                );

                // Habilitar input buscable de localidad
                if (localidadInput) {
                    localidadInput.removeAttribute('readonly');
                }
            });
    });
}


    /* =========================================================
       LUGAR DE NACIMIENTO
    ========================================================= */
    const paisSelect      = document.getElementById('paisNacimiento');
    const nacEntidad      = document.getElementById('entidadNacimientoSelect');
    const nacMunicipio    = document.getElementById('municipioNacimientoSelect');
    const nacLocalidad    = document.getElementById('localidadNacimientoSelect');

    const bloqueSelect    = document.getElementById('bloque-select-nacimiento');
    const bloqueInput     = document.getElementById('bloque-input-nacimiento');
    const inputsManual    = bloqueInput ? bloqueInput.querySelectorAll('input') : [];

    const setModoNacimiento = (modo) => {
        // modo: NONE | MEXICO | EXTRANJERO

        if (modo === 'NONE') {
            bloqueSelect.style.display = 'block';
            bloqueInput.style.display  = 'none';

            nacEntidad.disabled   = true;
            nacMunicipio.disabled = true;
            nacLocalidad.disabled = true;

            resetSelect(nacMunicipio, 'Seleccionar municipio', true);
            resetSelect(nacLocalidad, 'Seleccionar localidad', true);

            inputsManual.forEach(i => {
                i.disabled = true;
                i.value = '';
            });
        }

        if (modo === 'MEXICO') {
            bloqueSelect.style.display = 'block';
            bloqueInput.style.display  = 'none';

            nacEntidad.disabled   = false;
            nacMunicipio.disabled = true;
            nacLocalidad.disabled = true;

            resetSelect(nacMunicipio, 'Seleccionar municipio', true);
            resetSelect(nacLocalidad, 'Seleccionar localidad', true);

            inputsManual.forEach(i => {
                i.disabled = true;
                i.value = '';
            });
        }

        if (modo === 'EXTRANJERO') {
            bloqueSelect.style.display = 'none';
            bloqueInput.style.display  = 'block';

            nacEntidad.value = '';
            resetSelect(nacMunicipio, 'Seleccionar municipio', true);
            resetSelect(nacLocalidad, 'Seleccionar localidad', true);

            nacEntidad.disabled   = true;
            nacMunicipio.disabled = true;
            nacLocalidad.disabled = true;

            inputsManual.forEach(i => {
                i.disabled = false;
            });
        }
    };

    const paisNormalizado = () => {
        const opt = paisSelect?.options[paisSelect.selectedIndex];
        return opt?.dataset?.normalizado?.toUpperCase() || '';
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

    // Entidad → municipios (nacimiento)
    nacEntidad?.addEventListener('change', () => {
        if (paisNormalizado() !== 'MEXICO') return;

        const idEntidad = nacEntidad.value;

        resetSelect(nacMunicipio, 'Cargando...', true);
        resetSelect(nacLocalidad, 'Seleccionar municipio', true);

        if (!idEntidad) return;

        fetch(`/api/municipios/${idEntidad}`)
            .then(r => r.json())
            .then(data => {
                fillSelect(nacMunicipio, 'Seleccionar', data, 'idMunicipio', 'nombreMunicipio');
            });
    });

    // Municipio → localidades (nacimiento)
    nacMunicipio?.addEventListener('change', () => {
        if (paisNormalizado() !== 'MEXICO') return;

        const idMunicipio = nacMunicipio.value;

        resetSelect(nacLocalidad, 'Cargando...', true);

        if (!idMunicipio) return;

        fetch(`/api/localidades/${idMunicipio}`)
            .then(r => r.json())
            .then(data => {
                fillSelect(nacLocalidad, 'Seleccionar', data, 'idLocalidad', 'nombreLocalidad');
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

            // Si está bloqueado, no permitir escribir
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

                        // Disparar change para cascadas (fetch)
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
           (cuando fetch habilita municipio / localidad)
        ===================================================== */
        const observer = new MutationObserver(syncDisabled);
        observer.observe(select, {
            attributes: true,
            attributeFilter: ['disabled']
        });
    }

    /* =====================================================
       INICIALIZAR TODOS LOS SELECTS BUSCABLES
    ===================================================== */
    document
        .querySelectorAll('.select-buscable-wrapper')
        .forEach(initSelectBuscable);

});
</script>

</body>
</html>