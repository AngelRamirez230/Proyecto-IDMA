<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Estudiante</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @include('layouts.barraNavegacion')

    <form action="{{ route('estudiantes.update', $estudiante->idEstudiante) }}" method="POST" class="formulario">
    @csrf
    @method('PUT')

        <h1 class="titulo-form">Modificar estudiante</h1>

        {{-- ======================================================
            DATOS PERSONALES
        ======================================================= --}}
        <h3 class="subtitulo-form">Datos personales</h3>
        
        <div class="form-group">
            <label for="primer_nombre">Primer nombre:</label>
            <input
                type="text"
                id="primer_nombre"
                name="primer_nombre"
                class="input-mediano"
                placeholder="Ingresa el primer nombre"
                value="{{ $estudiante->usuario->primerNombre}}"
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
                value="{{$estudiante->usuario->segundoNombre }}"
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
                value="{{$estudiante->usuario->primerApellido}}"
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
                value="{{ $estudiante->usuario->segundoApellido}}"
            >
            <x-error-field field="segundo_apellido" />
        </div>

        {{-- SEXO --}}
        <div class="form-group">
            <label for="idSexo">Sexo:</label>
            <select id="idSexo" name="idSexo" class="select" required>

                <option value="" disabled>
                    Seleccionar
                </option>

                @foreach($sexos as $sx)
                    <option
                        value="{{ $sx->idSexo }}"
                        {{ old('idSexo', $estudiante->usuario->idSexo) == $sx->idSexo ? 'selected' : '' }}
                    >
                        {{ $sx->nombreSexo }}
                    </option>
                @endforeach

            </select>
            <x-error-field field="idSexo" />
        </div>

        {{-- ESTADO CIVIL --}}
        <div class="form-group">
            <label for="idEstadoCivil">Estado civil:</label>
            <select id="idEstadoCivil" name="idEstadoCivil" class="select" required>
                <option value="" disabled>Seleccionar</option>

                @foreach($estadosCiviles as $ec)
                    <option value="{{ $ec->idEstadoCivil }}"
                        {{ $estudiante->usuario->idEstadoCivil == $ec->idEstadoCivil ? 'selected' : '' }}>
                        {{ $ec->nombreEstadoCivil }}
                    </option>
                @endforeach
            </select>
            <x-error-field field="idEstadoCivil" />
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
                value="{{ $estudiante->usuario->telefono }}"
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
                placeholder="Ingresa número de teléfono fijo"
                value="{{ $estudiante->usuario->telefonoFijo }}"
            >
            <x-error-field field="telefonoFijo" />
        </div>


        {{-- CORREO INSTITUCIONAL --}}
        <div class="form-group">
            <label for="correoInstitucional">Correo institucional:</label>
            <input
                type="email"
                id="correoInstitucional"
                name="correoInstitucional"
                class="input-mediano"
                placeholder="ejemplo@idma.edu.mx"
                value="{{ $estudiante->usuario->correoInstitucional }}"
            >
            <x-error-field field="correoInstitucional" />
        </div>


        {{-- CONTRASEÑA --}}
        <div class="form-group">
            <label for="contraseña">Contraseña:</label>
            <input
                type="password"
                id="contraseña"
                name="contraseña"
                class="input-chico"
                placeholder="Solo si desea cambiarla"
            >
            <x-error-field field="contraseña" />
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
                value="{{ $estudiante->usuario->nombreUsuario }}"
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
                class="input-date"
                value="{{ $estudiante->usuario->fechaDeNacimiento
                    ? \Carbon\Carbon::parse($estudiante->usuario->fechaDeNacimiento)->format('Y-m-d')
                    : '' }}"
            >
            <x-error-field field="fechaNacimiento" />
        </div>


        {{-- CURP --}}
        <div class="form-group">
            <label for="CURP">CURP:</label>
            <input
                type="text"
                id="CURP"
                name="CURP"
                class="input-chico"
                placeholder="CURP"
                value="{{ $estudiante->usuario->CURP }}"
            >
            <x-error-field field="CURP" />
        </div>

        {{-- RFC --}}
        <div class="form-group">
            <label for="RFC">RFC:</label>
            <input
                type="text"
                id="RFC"
                name="RFC"
                class="input-chico"
                placeholder="RFC"
                placeholder="RFC"
                value="{{ $estudiante->usuario->RFC }}"
            >
            <x-error-field field="RFC" />
        </div>


        {{-- CORREO PERSONAL --}}
        <div class="form-group">
            <label for="correoElectronico">Correo electrónico:</label>
            <input
                type="email"
                id="correoElectronico"
                name="correoElectronico"
                class="input-mediano"
                placeholder="ejemplo@correo.com"
                value="{{ $estudiante->usuario->correoElectronico }}"
            >
            <x-error-field field="correoElectronico" />
        </div>


        <h3 class="subtitulo-form">Datos académicos</h3>

        {{-- Matricula Alfanumerica --}}
        <div class="form-group">
            <label>Matrícula alfanumérica:</label>
            <input
                type="text"
                class="input-mediano"
                value="{{ $estudiante->matriculaAlfanumerica }}"
                readonly
            >
        </div>

        {{-- Matricula númerica --}}
        <div class="form-group">
            <label>Matrícula numérica:</label>
            <input
                type="text"
                class="input-mediano"
                value="{{ $estudiante->matriculaNumerica }}"
                readonly
            >
        </div>


        {{-- Plan de estudios --}}
        <div class="form-group">
            <label for="idPlanDeEstudios">Plan de estudios:</label>

            <select class="select select-disabled-black" disabled>
                @foreach($planes as $plan)
                    <option value="{{ $plan->idPlanDeEstudios }}"
                        {{ $estudiante->idPlanDeEstudios == $plan->idPlanDeEstudios ? 'selected' : '' }}>
                        {{ $plan->nombrePlanDeEstudios }}
                    </option>
                @endforeach
            </select>
            <x-error-field field="idPlanDeEstudios" />
        </div>



        {{-- Licenciatura --}}
        <div class="form-group">
            <label for="licenciatura">Licenciatura:</label>
            <input
                type="text"
                id="licenciatura"
                class="input-mediano"
                value="{{ $estudiante->planDeEstudios->licenciatura?->nombreLicenciatura }}"
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
                value="{{ $estudiante->grado }}"
                min="1"
                max="9"
            >
            <x-error-field field="grado" />
        </div>


        {{-- Generación --}}
        <div class="form-group">
            <label for="generacion">Generación:</label>

            <input
                type="text"
                id="generacion"
                class="input-mediano"
                value="{{ $estudiante->generacion->nombreGeneracion }}"
                readonly
            >
        </div>

        {{-- Tipo de Inscripcion --}}
        <div class="form-group">
            <label for="idTipoDeInscripcion">Tipo de inscripción:</label>

            <select
                id="idTipoDeInscripcion"
                name="idTipoDeInscripcion"
                class="select select-buscable"
                required
            >
                <option value="" disabled>Seleccionar</option>

                @foreach($tipoInscripcion as $tipo)
                    <option
                        value="{{ $tipo->idTipoDeInscripcion }}"
                        {{ $estudiante->idTipoDeInscripcion == $tipo->idTipoDeInscripcion ? 'selected' : '' }}
                    >
                        {{ $tipo->nombreTipoDeInscripcion }}
                    </option>
                @endforeach
            </select>

            <x-error-field field="idTipoDeInscripcion" />
        </div>

        <h3 class="subtitulo-form">Datos del domicilio</h3>

            {{-- ENTIDAD --}}
        <div class="form-group">
            <label>Entidad:</label>
            <select id="entidad" name="entidad" class="select select-buscable">
                <option value="">Seleccionar</option>
                @foreach($entidades as $e)
                    <option value="{{ $e->idEntidad }}"
                        {{ $estudiante->usuario->domicilio?->localidad?->municipio?->entidad?->idEntidad == $e->idEntidad ? 'selected' : ''  }}
                        >
                        {{ $e->nombreEntidad }}</option>
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
                    value="{{ $estudiante->usuario->domicilio?->localidad?->municipio?->nombreMunicipio ?? '' }}"
                    readonly
                >

                <ul class="select-buscable-list"></ul>

                <select
                    id="municipio"
                    name="municipio"
                    hidden
                    disabled
                >
                    <option value="">Seleccionar</option>
                    @if($estudiante->usuario->domicilio?->localidad?->municipio)
                        <option
                            value="{{ $estudiante->usuario->domicilio->localidad->municipio->idMunicipio }}"
                            selected
                        >
                            {{ $estudiante->usuario->domicilio->localidad->municipio->nombreMunicipio }}
                        </option>
                    @endif
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
                    value="{{ $estudiante->usuario->domicilio?->localidad?->nombreLocalidad ?? '' }}"
                    readonly
                >

                <ul class="select-buscable-list"></ul>

                <select
                    id="localidad"
                    name="localidad"
                    hidden
                    disabled
                >
                    <option value="">Seleccionar</option>
                    @if($estudiante->usuario->domicilio?->localidad)
                        <option
                            value="{{ $estudiante->usuario->domicilio->localidad->idLocalidad }}"
                            selected
                        >
                            {{ $estudiante->usuario->domicilio->localidad->nombreLocalidad }}
                        </option>
                    @endif
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
                value="{{ $estudiante->usuario->domicilio->colonia ?? ''  }}"
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
                value="{{ $estudiante->usuario->domicilio->codigoPostal ?? '' }}"
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
                value="{{ $estudiante->usuario->domicilio->calle ?? '' }}"
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
                value="{{ $estudiante->usuario->domicilio->numeroExterior ?? '' }}"
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
                value="{{ $estudiante->usuario->domicilio->numeroInterior ?? '' }}"
            >
            <x-error-field field="numeroInterior" />
        </div>

        <h3 class="subtitulo-form">Lugar de nacimiento</h3>

        <div class="form-group">
            <label for="paisNacimiento">País:</label>
            <select id="paisNacimiento" name="paisNacimiento" class="select select-buscable">
                <option value="">Seleccionar</option>
                @foreach($paises as $pais)
                    <option
                        value="{{ $pais->idPais }}"
                        data-normalizado="{{ $pais->nombrePaisNormalizado }}"
                        {{ 
                            $estudiante->usuario->localidadNacimiento?->municipio?->entidad?->pais?->idPais 
                            == $pais->idPais 
                            ? 'selected' 
                            : '' 
                        }}
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
                        <option value="{{ $e->idEntidad }}"
                            {{ $estudiante->usuario->localidadNacimiento?->municipio?->entidad?->idEntidad == $e->idEntidad ? 'selected' : ''  }}
                            >
                            {{ $e->nombreEntidad }}</option>
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
                        placeholder="Seleccione municipio"
                        data-target="municipioNacimientoSelect"
                        autocomplete="off"
                        value="{{ $estudiante->usuario->localidadNacimiento?->municipio?->nombreMunicipio ?? '' }}"
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

                        @if($estudiante->usuario->localidadNacimiento?->municipio)
                            <option
                                value="{{ $estudiante->usuario->localidadNacimiento->municipio->idMunicipio }}"
                                selected
                            >
                                {{ $estudiante->usuario->localidadNacimiento->municipio->nombreMunicipio }}
                            </option>
                        @endif
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
                        value="{{ $estudiante->usuario->localidadNacimiento?->nombreLocalidad ?? '' }}"
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
                        @if($estudiante->usuario->localidadNacimiento)
                            <option
                                value="{{ $estudiante->usuario->localidadNacimiento->idLocalidad }}"
                                selected
                            >
                                {{ $estudiante->usuario->localidadNacimiento->nombreLocalidad }}
                            </option>
                        @endif

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




                    

        {{-- ======================================================
            BOTONES
        ======================================================= --}}
        <div class="form-group">
            <button type="submit" name="accion"  value="guardar" class="btn-boton-formulario">
                Guardar cambios
            </button>

            <button type="submit"
                    name="accion"
                    value="Suspender/Habilitar"
                    class="btn-boton-formulario">
                {{ $usuario->idestatus == 1 ? 'Suspender' : 'Habilitar' }}
            </button>

            <a href="{{ route('consultaEstudiantes') }}"
            class="btn-boton-formulario btn-cancelar">
                Cancelar
            </a>
        </div>

    </form>
    @include('layouts.alta')

</body>
</html>
