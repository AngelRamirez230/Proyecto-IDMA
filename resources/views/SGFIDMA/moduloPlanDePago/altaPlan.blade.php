<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Plan de Pago</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @include('layouts.barraNavegacion')
    

    <form action="{{ route('planes.store') }}" method="POST" class="formulario2">
        @csrf
        <h1 class="titulo-form2">Alta de plan de pago</h1>

        {{-- NOMBRE DEL PLAN DE PAGO --}}
        <div class="form-group2">
            <label for="nombrePlan">Nombre del plan de pago:</label>
            <input
                type="text"
                id="nombrePlan"
                name="nombrePlan"
                class="input-grande2"
                placeholder="Ingresa el nombre del plan de pago"
                value="{{ old('nombrePlan') }}"
                required
            >
            <x-error-field field="nombrePlan" />
        </div>

        <h1 class="selecciona-conceptos">Seleciona los conceptos:</h1>

        <!-- Lista de conceptos -->
        <div class="lista-conceptos">

            @foreach ($conceptos as $concepto)
                @if ($concepto->idEstatus == 1)

                    <div class="concepto-item">

                        <!-- Botón menos -->
                        <button type="button"
                                class="btn-cantidad"
                                onclick="cambiarCantidad('{{ $concepto->idConceptoDePago }}', -1)">
                            <img src="{{ asset('imagenes/iconoMenos.png') }}" alt="menos" class="iconos">
                        </button>

                        <!-- Nombre del concepto -->
                        <span class="nombre-concepto">
                            {{ $concepto->nombreConceptoDePago }}
                        </span>

                        <!-- Botón más -->
                        <button type="button"
                                class="btn-cantidad"
                                onclick="cambiarCantidad('{{ $concepto->idConceptoDePago }}', 1)">
                            <img src="{{ asset('imagenes/iconoMas.png') }}" alt="mas" class="iconos">
                        </button>

                        <!-- Input cantidad -->
                        <input type="number"
                        name="cantidades[{{ $concepto->idConceptoDePago }}]"
                        id="cantidad_{{ $concepto->idConceptoDePago }}"
                        class="cantidad-input"
                        value="{{ old('cantidades.' . $concepto->idConceptoDePago, 0) }}"
                        min="0"
                        step="1">

                    </div>
                @endif
            @endforeach

        </div>

        <!-- Botones Guardar / Cancelar -->
        <div class="form-group2">
            <button type="submit" class="btn-boton-formulario2">Guardar</button>
            <a href="{{ route('apartadoPlanDePago') }}" class="btn-boton-formulario2 btn-cancelar2">Cancelar</a>
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

    <!-- Script para sumar/restar -->
    <script>

        function cambiarCantidad(id, cambio) {
            const input = document.getElementById("cantidad_" + id);
            let valor = parseInt(input.value);

            valor += cambio;
            if (valor < 0) valor = 0;

            input.value = valor;
        }
    </script>

</body>
</html>
