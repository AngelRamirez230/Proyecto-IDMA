<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificación de beca</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <form action="{{ route('becas.update', $beca->idBeca) }}" method="POST" class="formulario2">
    @csrf
    @method('PUT') 

        <h1 class="titulo-form2">Modificación de beca</h1>

        {{-- NOMBRE DE BECA (SOLO LECTURA) --}}
        <div class="form-group2">
            <label for="nombreBeca">Nombre de Beca:</label>
            <input
                type="text"
                id="nombreBeca"
                name="nombreBeca"
                class="input-grande2 input-bloqueado2"
                value="{{ old('nombreBeca', $beca->nombreDeBeca) }}"
                readonly
            >
            <x-error-field field="nombreBeca" />
        </div>

        {{-- PORCENTAJE DE DESCUENTO --}}
        <div class="form-group2">
            <label for="porcentajeBeca">Porcentaje de descuento:</label>

            <div class="contenedor-input-icono2">
                <input
                    type="text"
                    id="porcentajeBeca"
                    name="porcentajeBeca"
                    class="input-chico2"
                    value="{{ old('porcentajeBeca', $beca->porcentajeDeDescuento) }}"
                    required
                >

                <img
                    src="{{ asset('imagenes/IconoPorcentaje.png') }}"
                    class="icono-input-img2"
                    alt="icono"
                >
            </div>

            <x-error-field field="porcentajeBeca" />
            <span id="porcentajeError" class="mensajeError"></span>
        </div>


        <div class="form-group2">
            <button type="submit"  name="accion" value="guardar" class="btn-boton-formulario2">Guardar cambios</button>
            <button type="submit"
                    name="accion"
                    value="Suspender/Habilitar"
                    class="btn-boton-formulario2">
                {{ $beca->idEstatus == 1 ? 'Suspender' : 'Habilitar' }}
            </button>
            <a href="{{ route('consultaBeca') }}" class="btn-boton-formulario2 btn-cancelar2">Cancelar</a>
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


</body>
</html>