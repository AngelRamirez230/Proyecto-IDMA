<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de beca</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <form id="formBeca" action="{{ route('becas.store') }}" method="POST" class="formulario2">
    @csrf

        <h1 class="titulo-form2">Alta de beca</h1>

        {{-- NOMBRE DE BECA --}}
        <div class="form-group2">
            <label for="nombreBeca">Nombre de Beca:</label>
            <input
                type="text"
                id="nombreBeca"
                name="nombreBeca"
                class="input-grande2"
                placeholder="Ingresa el nombre de la beca"
                value="{{ old('nombreBeca') }}"
                required
            >
            <x-error-field field="nombreBeca" />
        </div>

        {{-- PORCENTAJE DE DESCUENTO --}}
        <div class="form-group2 input-con-icono">
            <label for="porcentajeBeca">Porcentaje de descuento:</label>

            <div class="contenedor-input-icono2">
                <input
                    type="text"
                    id="porcentajeBeca"
                    name="porcentajeBeca"
                    class="input-chico2"
                    placeholder="Porcentaje de descuento"
                    value="{{ old('porcentajeBeca') }}"
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

        {{-- BOTONES --}}
        <div class="form-group2">
            <button type="submit" class="btn-boton-formulario2">Guardar</button>
            <a href="{{ route('apartadoBecas') }}" class="btn-boton-formulario2 btn-cancelar2">Cancelar</a>
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