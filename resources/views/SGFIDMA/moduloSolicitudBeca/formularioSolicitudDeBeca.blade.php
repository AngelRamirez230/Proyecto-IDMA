<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar beca</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @include('layouts.barraNavegacion')

    <form action="{{ route('solicitud-beca.store') }}" method="POST" enctype="multipart/form-data" class="formulario2">
    @csrf

        {{-- ================= REQUISITOS ================= --}}
        <div class="contenedor-requisitos">

            <h2 class="titulo-requisitos">Requisitos para renovación del beneficio</h2>

            <ol class="lista-numerada">
                <li>
                    Para la renovación del beneficio se requiere de lo siguiente:
                    <ol class="lista-letras">
                        <li>
                            Acreditar todas las materias de la carga académica correspondiente
                            al periodo escolar vigente de manera ordinaria, cumpliendo con el
                            promedio mínimo o mayor de <strong>8.5</strong> para licenciaturas
                            (sólo aplica para alumnos que renuevan su beca, apoyo o descuento).
                        </li>

                        <li>Demostrar situación económica que impide cubrir parcialmente las colegiaturas.</li>
                        <li>Registro de buena conducta.</li>
                        <li>No contar con adeudo de pagos de inscripción y colegiaturas.</li>
                        <li>Haber realizado el pago y el formulario de inscripción para el ciclo escolar a cursar.</li>
                    </ol>
                </li>

                <li>
                    Si se comprueba que se presenta documentación apócrifa, será retirado el beneficio
                    de beca, apoyo o descuento, debiendo pagar íntegramente las cuotas correspondientes.
                </li>
            </ol>

        </div>

        {{-- ================= DATOS DE LA BECA ================= --}}
        <input type="hidden" name="idBeca" value="{{ $beca->idBeca ?? '' }}">

        <div class="form-group2">
            <label for="nombreBeca">Nombre de Beca:</label>
            <input
                type="text"
                id="nombreBeca"
                class="input-grande2 input-bloqueado2"
                value="{{ $beca->nombreDeBeca ?? '' }}"
                readonly
            >
        </div>

        <div class="form-group2">
            <label for="porcentajeBeca">Porcentaje de descuento:</label>
            <div class="contenedor-input-icono2">
                <input
                    type="text"
                    id="porcentajeBeca"
                    class="input-chico2 input-bloqueado2"
                    value="{{ $beca->porcentajeDeDescuento ?? '' }}"
                    readonly
                >
                <img src="{{ asset('imagenes/IconoPorcentaje.png') }}" class="icono-input-img2" alt="icono">
            </div>
        </div>

        {{-- ================= DATOS DEL ESTUDIANTE ================= --}}
        <div class="form-group2">
            <label for="promedio">
                ¿Cuál fue tu promedio en el semestre que acabas de cursar?
            </label>
            <input
                type="number"
                step="0.01"
                min="8.5"
                max="10"
                id="promedio"
                name="promedio"
                placeholder="Ejemplo: 9.5"
                class="input-chico2"
                required
            >
        </div>

        <div class="form-group2">
            <label for="examenExtraordinario">
                En el semestre cursado ¿Presentaste algún examen extraordinario? ¿Cuál?
            </label>
            <input
                type="text"
                id="examenExtraordinario"
                name="examenExtraordinario"
                class="input-grande2"
                placeholder="Especifica o deja vacío"
            >
        </div>

        {{-- ================= TEXTO INFORMATIVO ================= --}}
        <div class="textoNormal">
            <p><strong>Solicitud de renovación de BECA</strong></p>
            <p>Llenado en <strong>tinta azul y a mano</strong></p>
            <p>
                Escaneo de buena calidad <strong>(no se admiten fotografías).
                El escaneo puedes hacerlo en una papelería, te pedimos lo hagas en la mejor calidad posible.</strong>
            </p>
            <p>Formato PDF</p>
        </div>

        <a href="{{ asset('storage/documentos/becas/DocumentoDePrueba.pdf') }}" class="link-descarga" download>
            Descárguelo aquí
        </a>

        <div class="subir-documento">
            <label for="documento_solicitud" class="label-documento">
                Adjuntar solicitud de renovación de BECA
            </label>

            <div class="contenedor-archivo">
                <input
                    type="file"
                    id="documento_solicitud"
                    name="documento_solicitud"
                    accept=".pdf"
                    hidden
                    required
                    onchange="validarPDF(this, 'nombreArchivoSolicitud')"
                >

                <button
                    type="button"
                    class="boton-subir"
                    onclick="document.getElementById('documento_solicitud').click()"
                >
                    Seleccionar archivo
                </button>

                <span id="nombreArchivoSolicitud" class="nombre-archivo">
                    Ningún archivo seleccionado
                </span>
            </div>
        </div>


        {{-- ================= CASO ESPECIAL ================= --}}
        <div>
            <div class="textoNormal">
                <p>Beca por Padre o Madre soltera</p>
            </div>

            <div class="textoNormal">
                <p>
                    Si tu solicitud de beca es por "padre o madre soltera", adjunta a continuación
                    tus documentos probatorios en un solo archivo PDF:
                </p>

                <li>- Constancia de Padre o Madre soltera.</li>
                <li>- Documento que acredite la tutoría legal.</li>
                <li>- Puedes agregar otro documento que consideres oportuno.</li>
            </div>

            <div class="textoNormal">
                <p><strong>Puede ser alguno de esos, no necesariamente todos</strong></p>
            </div>
        </div>

        {{-- ================= SUBIDA DE DOCUMENTO ================= --}}
        <div class="subir-documento">
            <label for="documento_adicional" class="label-documento">
                Adjuntar documento
            </label>

            <div class="contenedor-archivo">
                <input
                    type="file"
                    id="documento_adicional"
                    name="documento_adicional"
                    accept=".pdf"
                    hidden
                    required
                    onchange="validarPDF(this, 'nombreArchivoAdicional')"
                >

                <button
                    type="button"
                    class="boton-subir"
                    onclick="document.getElementById('documento_adicional').click()"
                >
                    Seleccionar archivo
                </button>

                <span id="nombreArchivoAdicional" class="nombre-archivo">
                    Ningún archivo seleccionado
                </span>
            </div>
        </div>


        {{-- ================= BOTONES ================= --}}
        <div class="form-group2">
            <button type="submit" class="btn-boton-formulario2">
                Solicitar beca
            </button>

            <a href="{{ route('consultaBeca') }}"
            class="btn-boton-formulario2 btn-cancelar2">
                Cancelar
            </a>
        </div>

    </form>

    {{-- ================= VALIDACIÓN PDF ================= --}}
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
