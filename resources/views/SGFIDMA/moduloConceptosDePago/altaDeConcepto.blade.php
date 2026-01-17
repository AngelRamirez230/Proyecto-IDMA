<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de concepto de pago</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <form action="{{ route('concepto.store') }}" method="POST" class="formulario2">
    @csrf

        <h1 class="titulo-form2">Alta de concepto de pago</h1>

        {{-- NOMBRE DEL CONCEPTO --}}
        <div class="form-group2">
            <label for="nombreConcepto">Nombre del concepto de pago:</label>
            <input
                type="text"
                id="nombreConcepto"
                name="nombreConcepto"
                class="input-grande2"
                placeholder="Ingresa el nombre del concepto de pago"
                value="{{ old('nombreConcepto') }}"
                required
            >
            <x-error-field field="nombreConcepto" />
        </div>

        {{-- COSTO --}}
        <div class="form-group2">
            <label for="costo">Costo:</label>
            <input
                type="text"
                id="costo"
                name="costo"
                class="input-chico2"
                placeholder="Ingresa el costo"
                value="{{ old('costo') }}"
                required
            >
            <x-error-field field="costo" />
            <span id="costoError" class="mensajeError"></span>
        </div>

        {{-- UNIDAD --}}
        <div class="form-group2">
            <label for="unidad">Unidad:</label>
            <select
                id="unidad"
                name="unidad"
                class="select2"
                required
            >
                <option value="" disabled {{ old('unidad') ? '' : 'selected' }}>
                    Seleccionar
                </option>

                @foreach ($unidades as $u)
                    <option
                        value="{{ $u->idTipoDeUnidad }}"
                        {{ old('unidad') == $u->idTipoDeUnidad ? 'selected' : '' }}
                    >
                        {{ $u->nombreUnidad }}
                    </option>
                @endforeach
            </select>
            <x-error-field field="unidad" />
        </div>

        {{-- BOTONES --}}
        <div class="form-group2">
            <button type="submit" class="btn-boton-formulario2">Guardar</button>
            <a href="{{ route('apartadoConceptos') }}" class="btn-boton-formulario2 btn-cancelar2">Cancelar</a>
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