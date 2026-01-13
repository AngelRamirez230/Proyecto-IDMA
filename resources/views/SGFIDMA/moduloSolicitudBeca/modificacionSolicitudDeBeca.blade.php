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

        <section class="consulta-tabla-contenedor">
            <table class="tabla" style="margin-bottom: 18px;">
                <tbody class="tabla-cuerpo">

                    <tr class="tabla-fila">
                        <td><strong>Nombre del estudiante</strong></td>
                        <td>
                            {{ 
                                trim(
                                    ($solicitud->estudiante->usuario->primerNombre ?? '') . ' ' .
                                    ($solicitud->estudiante->usuario->segundoNombre ?? '')
                                ) ?: 'N/D'
                            }}
                        </td>

                        <td><strong>Apellidos</strong></td>
                        <td>
                            {{ 
                                trim(
                                    ($solicitud->estudiante->usuario->primerApellido ?? '') . ' ' .
                                    ($solicitud->estudiante->usuario->segundoApellido ?? '')
                                ) ?: 'N/D'
                            }}
                        </td>
                    </tr>

                    <tr class="tabla-fila">
                        <td><strong>Correo electrónico</strong></td>
                        <td>{{ $solicitud->estudiante->usuario->correoElectronico ?? 'Sin correo electrónico' }}</td>

                        <td><strong>Generación</strong></td>
                        <td>{{ $solicitud->estudiante->generacion->nombreGeneracion ?? 'Sin generación definida' }}</td>
                    </tr>

                    <tr class="tabla-fila">
                        <td><strong>Ciclo escolar</strong></td>
                        <td>{{ 'Sin ciclo escolar definido' }}</td>

                        <td><strong>Semestre al que ingresará</strong></td>
                        <td>{{ ($solicitud->estudiante->grado ?? 0) + 1 }}</td>
                    </tr>

                    <tr class="tabla-fila">
                        <td><strong>Nombre de la beca</strong></td>
                        <td>{{ $solicitud->beca->nombreDeBeca ?? 'N/D' }}</td>

                        <td><strong>Porcentaje de descuento</strong></td>
                        <td>{{ $solicitud->beca->porcentajeDeDescuento ?? '—' }}%</td>
                    </tr>

                    <tr class="tabla-fila">
                        <td><strong>Promedio anterior</strong></td>
                        <td>{{ $solicitud->promedioAnterior ?? 'No registrado' }}</td>

                        <td><strong>Examen extraordinario</strong></td>
                        <td>{{ $solicitud->examenExtraordinario ?: 'Ninguno' }}</td>
                    </tr>

                </tbody>
            </table>
        </section>

        @estudiante
        <div class="form-group2"> 
            <label>Promedio anterior:</label> 
            <input type="number" 
            step="0.01" 
            min="8.5" 
            max="10" 
            name="promedio" 
            class="input-chico2"
            placeholder="Ejemplo: 9.5" 
            value="{{ old('promedio', $solicitud->promedioAnterior ?? '') }}" > 
        </div> 

        
        <div class="form-group2"> 
            <label> Examen extrairdinario: </label> 
            <input type="text" 
            name="examenExtraordinario" 
            class="input-chico2" 
            placeholder="Especifica o deja vacío"
            value="{{ old('examenExtraordinario', $solicitud->examenExtraordinario ?? '') }}" > 
        </div>
        

        @endestudiante



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
                        <td>Solicitud de renovación de BECA (PDF)</td>

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


        @admin
        <div class="form-group2">
            <label for="observaciones">Observaciones:</label>
            <textarea
                id="observaciones"
                name="observaciones"
                class="textarea-grande"
                maxlength="200"
                placeholder="Escribe las observaciones aquí..."
            >{{ old('observaciones', $solicitud->observacion) }}</textarea>
        </div>
        @endadmin


        @estudiante
        <div class="form-group2">
            <label>Observaciones:</label>
            <textarea
                class="textarea-grande"
                readonly
            >{{ $solicitud->observacion ?? 'Sin observaciones' }}</textarea>
        </div>
        @endestudiante




        {{-- ================= ACCIONES ================= --}}

        
        <div class="form-group2">

        @estudiante    
            <button type="submit" name="accion" value="guardar" class="btn-boton-formulario2">
                Guardar cambios
            </button>
        @endestudiante


        @admin
            <button type="submit" name="accion" value="aprobar" class="btn-boton-formulario2"
            {{ $solicitud->idEstatus == 6 ? 'disabled' : '' }}
            >
                Aprobar
            </button>
        
            <button type="submit" name="accion" value="rechazar" class="btn-boton-formulario2"
            {{ $solicitud->idEstatus == 7 ? 'disabled' : '' }}
            >
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
