<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación de pagos</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @include('layouts.barraNavegacion')
    

    <main class="consulta">

        <h1 class="titulo-form2">Validación de pagos pendientes</h1>

        <div class="detalle-usuario__header">

            <!-- BOTÓN SUBIR TXT -->
            <form action="{{ route('pagos.validarArchivo') }}" method="POST" enctype="multipart/form-data" class="upload-form">
                @csrf
                <div class="upload-container">

                    <label for="archivoTxt" class="btn-upload">
                        Seleccionar archivo
                    </label>

                    <input 
                        type="file" 
                        name="archivoTxt" 
                        id="archivoTxt" 
                        accept=".txt,.xlsx,.xls" 
                        required 
                        class="upload-input-hidden"
                    >

                    <span id="archivoNombre" class="archivo-nombre">
                        Ningún documento seleccionado
                    </span>

                    <!-- Botón validar -->
                    <button type="submit" class="btn-boton-formulario2 btn-accion">
                        Validar pagos
                    </button>

                </div>
            </form>
        </div>


        <!-- TABLA DE PAGOS PENDIENTES -->
        <section class="consulta-tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Nombre estudiante</th>
                        <th>Referencia de pago</th>
                        <th>Concepto de pago</th>
                        <th>Fecha límite</th>
                        <th>Fecha de pago</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pagos as $pago)
                        <tr>
                            <td>
                                {{ $pago->estudiante->usuario->primerNombre }}
                                {{ $pago->estudiante->usuario->segundoNombre }}
                                {{ $pago->estudiante->usuario->primerApellido }}
                                {{ $pago->estudiante->usuario->segundoApellido }}
                            </td>
                            <td>{{ $pago->Referencia }}</td>
                            <td>{{ $pago->concepto->nombreConceptoDePago }}</td>
                            <td>{{ $pago->fechaLimiteDePago?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $pago->fechaDePago?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $pago->estatus->nombreTipoDeEstatus ?? 'Pendiente' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No hay pagos pendientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <div class="paginacion">
            {{ $pagos->links() }}
        </div>
    </main>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('archivoTxt');
            const span  = document.getElementById('archivoNombre');

            if (input) {
                input.addEventListener('change', function () {
                    if (this.files && this.files.length > 0) {
                        span.textContent = this.files[0].name;
                    } else {
                        span.textContent = 'Ningún documento seleccionado';
                    }
                });
            }
        });
    </script>


</body>
</html>
