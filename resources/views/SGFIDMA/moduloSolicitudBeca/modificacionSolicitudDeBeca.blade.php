<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la solicitud de beca</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    
@include('layouts.barraNavegacion')

    <form action="{{ route('solicitud-beca.update', $solicitud->idSolicitudDeBeca) }}" method="POST" class="formulario2" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <h1 class="titulo-form2">Detalles de la solicitud de beca</h1>

        {{-- ================= DATOS DE LA BECA ================= --}}
        <input type="hidden" name="idBeca" value="{{ $solicitud->beca->idBeca }}">

        <div class="form-group2">
            <label>Nombre del estudiante:</label>
            <input
                type="text"
                class="input-grande2 input-bloqueado2"
                value="{{ $solicitud->estudiante->usuario->primerNombre}} {{$solicitud->estudiante->usuario->segundoNombre}}"
                readonly
            >
        </div>

        <div class="form-group2">
            <label>Apellidos:</label>
            <input
                type="text"
                class="input-grande2 input-bloqueado2"
                value="{{ $solicitud->estudiante->usuario->primerApellido }} {{ $solicitud->estudiante->usuario->segundoApellido }}"
                readonly
            >
        </div>

        <div class="form-group2">
            <label>Correo electrónico:</label>
            <input
                type="email"
                class="input-grande2 input-bloqueado2"
                value="{{ $solicitud->estudiante->usuario->correoElectronico }}"
                readonly
            >
        </div>

        <div class="form-group2">
            <label>Generación:</label>
            <input
                type="text"
                class="input-chico2 input-bloqueado2"
                value="{{ $solicitud->estudiante->generacion->nombreGeneracion }}"
                readonly
            >
        </div>

        <div class="form-group2">
            <label>Ciclo escolar:</label>
            <input
                type="text"
                class="input-chico2 input-bloqueado2"
                value=""
                readonly
            >
        </div>

        <div class="form-group2">
            <label>Semestre al que ingresará:</label>
            <input
                type="number"
                class="input-chico2 input-bloqueado2"
                value="{{ $solicitud->estudiante->grado }}"
                readonly
            >
        </div>

        <div class="form-group2">
            <label>Nombre de Beca:</label>
            <input
                type="text"
                class="input-grande2 input-bloqueado2"
                value="{{ $solicitud->beca->nombreDeBeca }}"
                readonly
            >
        </div>

        <div class="form-group2">
            <label>Porcentaje de descuento:</label>
            <div class="contenedor-input-icono2">
                <input
                    type="text"
                    class="input-chico2 input-bloqueado2"
                    value="{{ $solicitud->beca->porcentajeDeDescuento }}"
                    readonly
                >
                <img src="{{ asset('imagenes/IconoPorcentaje.png') }}" class="icono-input-img2">
            </div>
        </div>

        {{-- ================= DATOS DEL ESTUDIANTE ================= --}}
        <div class="form-group2">
            <label>¿Cuál fue tu promedio en el semestre que acabas de cursar?</label>
            <input
                type="number"
                step="0.01"
                min="8.5"
                max="10"
                name="promedio"
                class="input-chico2"
                value="{{ old('promedio', $solicitud->promedioAnterior) }}"
                
            >
        </div>

        <div class="form-group2">
            <label>
                En el semestre cursado ¿Presentaste algún examen extraordinario? ¿Cuál?
            </label>
            <input
                type="text"
                name="examenExtraordinario"
                class="input-grande2"
                value="{{ old('examenExtraordinario', $solicitud->examenExtraordinario) }}"
            >
        </div>


        {{-- ================= DOCUMENTACIÓN DE SOLICITUD ================= --}}
        <section class="consulta-tabla-contenedor">

            <table class="tabla" id="tablaDocumentacionSolicitud">

                {{-- ================= ENCABEZADO ================= --}}
                <thead>
                    <tr class="tabla-encabezado">
                        <th>Tipo de documento</th>
                        <th>Archivo</th>

                        @estudiante
                            <th>Acciones</th>
                            <th>Archivo seleccionado</th>
                        @endestudiante
                    </tr>
                </thead>

                {{-- ================= CUERPO ================= --}}
                <tbody class="tabla-cuerpo">

                    {{-- ================= SOLICITUD DE BECA ================= --}}
                    <tr>
                        <td>Solicitud de beca (PDF)</td>

                        {{-- ARCHIVO ACTUAL --}}
                        <td>
                            @if($docSolicitud)
                                <a
                                    href="{{ Storage::url($docSolicitud->ruta) }}"
                                    target="_blank"
                                    class="btn-boton-formulario2 btn-accion"
                                >
                                    Ver
                                </a>
                            @else
                                <span class="texto-bloqueado">
                                    No cargado
                                </span>
                            @endif
                        </td>

                        @estudiante
                            {{-- ACCIONES --}}
                            <td>
                                <div class="tabla-acciones">
                                    <input
                                        type="file"
                                        id="documento_solicitud"
                                        name="documento_solicitud"
                                        accept=".pdf"
                                        hidden
                                        onchange="validarPDF(this, 'nombreArchivoSolicitud')"
                                    >

                                    <button
                                        type="button"
                                        class="btn-boton-formulario2 btn-accion"
                                        onclick="document.getElementById('documento_solicitud').click()"
                                    >
                                        Cambiar archivo
                                    </button>
                                </div>
                            </td>

                            {{-- ARCHIVO SELECCIONADO --}}
                            <td>
                                <span id="nombreArchivoSolicitud" class="nombre-archivo">
                                    Ningún archivo seleccionado
                                </span>
                            </td>
                        @endestudiante
                    </tr>

                    {{-- ================= DOCUMENTO ADICIONAL ================= --}}
                    <tr>
                        <td>Documento adicional (PDF)</td>

                        {{-- ARCHIVO ACTUAL --}}
                        <td>
                            @if($docAdicional)
                                <a
                                    href="{{ Storage::url($docAdicional->ruta) }}"
                                    target="_blank"
                                    class="btn-boton-formulario2 btn-accion"
                                >
                                    Ver
                                </a>
                            @else
                                <span class="texto-bloqueado">
                                    No cargado
                                </span>
                            @endif
                        </td>

                        @estudiante
                            {{-- ACCIONES --}}
                            <td>
                                <div class="tabla-acciones">
                                    <input
                                        type="file"
                                        id="documento_adicional"
                                        name="documento_adicional"
                                        accept=".pdf"
                                        hidden
                                        onchange="validarPDF(this, 'nombreArchivoAdicional')"
                                    >

                                    <button
                                        type="button"
                                        class="btn-boton-formulario2 btn-accion"
                                        onclick="document.getElementById('documento_adicional').click()"
                                    >
                                        Cambiar archivo
                                    </button>
                                </div>
                            </td>

                            {{-- ARCHIVO SELECCIONADO --}}
                            <td>
                                <span id="nombreArchivoAdicional" class="nombre-archivo">
                                    Ningún archivo seleccionado
                                </span>
                            </td>
                        @endestudiante
                    </tr>

                </tbody>
            </table>
        </section>



        {{-- ================= ACCIONES ================= --}}

        
        <div class="form-group2">

        @estudiante    
            <button type="submit" name="accion" value="guardar" class="btn-boton-formulario2">
                Guardar cambios
            </button>
        @endestudiante


        @admin
            <button type="submit" name="accion" value="aprobar" class="btn-boton-formulario2">
                Aprobar
            </button>
        
            <button type="submit" name="accion" value="rechazar" class="btn-boton-formulario2">
                Rechazar
            </button>
        @endadmin

            <a
                href="{{ route('consultaSolicitudBeca') }}"
                class="btn-boton-formulario2 btn-cancelar2"
            >
                Cancelar
            </a>
        </div>

    </form>

    <script>
    function validarPDF(input, idSpan) {
        const archivo = input.files[0];
        const nombreArchivo = document.getElementById(idSpan);

        if (!archivo) {
            nombreArchivo.textContent = "Ningún archivo seleccionado";
            return;
        }

        if (archivo.type !== "application/pdf") {
            alert("Solo se permiten archivos PDF");
            input.value = "";
            nombreArchivo.textContent = "Ningún archivo seleccionado";
            return;
        }

        nombreArchivo.textContent = archivo.name;
    }
    </script>

</body>
</html>
