<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de estudiante</title>
    @vite(['resources/css/app.css'])
</head>

<body>
@include('layouts.barraNavegacion')

    <main class="form-container">
        {{-- FORMULARIO --}}
        <form action="{{ route('estudiantes.store') }}" method="POST" class="formulario">
            @csrf

                {{-- TÍTULO DEL FORMULARIO --}}
            <h1 class="titulo-form">Alta de estudiante</h1>

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

            <h3 class="subtitulo-form">Datos académicos</h3>

            {{-- Matricula Alfanumerica --}}
            <div class="form-group">
                <label for="matriculaAlfanumerica">Matrícula alfanumérica:</label>
                <input
                    type="text"
                    id="matriculaAlfanumerica"
                    name="matriculaAlfanumerica"
                    class="input-mediano"
                    placeholder="Matricula alfanumerica"
                    value="{{ old('matriculaAlfanumerica') }}"
                >
                <x-error-field field="matriculaAlfanumerica" />
            </div>

            {{-- Matricula númerica --}}
            <div class="form-group">
                <label for="matriculaNumerica">Matrícula númerica:</label>
                <input
                    type="text"
                    id="matriculaNumerica"
                    name="matriculaNumerica"
                    class="input-mediano"
                    placeholder="Matrícula númerica"
                    value="{{ old('matriculaNumerica') }}"
                >
                <x-error-field field="matriculaNumerica" />
            </div>


            {{-- Plan de estudios --}}
            <div class="form-group">
                <label for="planEstudios">Plan de estudios:</label>
                <select id="planEstudios" name="planEstudios" class="select" required>
                    <option value="" disabled {{ old('planEstudios') ? '' : 'selected' }}>
                        Seleccionar
                    </option>

                    @foreach($planes as $plan)
                        <option
                            value="{{ $plan->idPlanDeEstudios }}"
                            licenciatura="{{ $plan->licenciatura->nombreLicenciatura ?? '' }}"
                            {{ old('planEstudios') == $plan->idPlanDeEstudios ? 'selected' : '' }}
                        >
                            {{ $plan->nombrePlanDeEstudios }}
                        </option>
                    @endforeach
                </select>

                <x-error-field field="planEstudios" />
            </div>


            {{-- Licenciatura --}}
            <div class="form-group">
                <label for="licenciatura">Licenciatura:</label>
                <input
                    type="text"
                    id="licenciatura"
                    name="licenciatura"
                    class="input-mediano"
                    readonly
                >
            </div>

            {{-- Grado --}}
            <div class="form-group">
                <label for="grado">Grado:</label>
                <input
                    type="number"
                    id="grado"
                    name="grado"
                    class="input-chico"
                    placeholder="Grado"
                    value="{{ old('grado') }}"
                    min="1"
                    max="9"
                    step="1"
                >
                <x-error-field field="grado" />
            </div>

            <div class="form-group">
                <label for="generacion">Generación:</label>

                @if($generacionActual)
                    <input
                        type="text"
                        class="input-mediano"
                        value="{{ $generacionActual->nombreGeneracion }}"
                        readonly
                    >
                    <input
                        type="hidden"
                        name="generacion"
                        value="{{ $generacionActual->idGeneracion }}"
                    >
                @else
                    <input
                        type="text"
                        class="input-mediano"
                        value="No existe generación activa"
                        readonly
                    >
                    <p class="texto-alerta">
                        Debe crearse una nueva generación (Marzo o Septiembre)
                    </p>
                @endif
            </div>

                {{-- Tipo de Inscripcion --}}
            <div class="form-group">
                <label>Tipo de inscripcion:</label>

                <select id="tipoInscripcion"
                        name="tipoInscripcion"
                        class="select select-buscable">

                    <option value="">Seleccionar</option>

                    @foreach($tipoInscripcion as $tipo)
                        <option value="{{ $tipo->idTipoDeInscripcion }}">
                            {{ $tipo->nombreTipoDeInscripcion }}
                        </option>
                    @endforeach
                </select>

                <x-error-field field="tipoInscripcion" />
            </div>


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
                        <option value="">Seleccionar país</option>
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
            <div class="form-group">
                <button type="submit" class="btn-boton-formulario">Guardar</button>
                <a href="{{ route('apartadoUsuarios') }}" class="btn-boton-formulario btn-cancelar">
                    Cancelar
                </a>
            </div>
        </form>

    </main>

@include('layouts.alta')

</body>
</html>
