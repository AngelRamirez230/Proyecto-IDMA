<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Plan de Pago</title>
    @vite(['resources/css/app.css', 'resources/css/plan.css']) <!-- CSS general + CSS de plan -->
</head>
<body>

    @include('layouts.barraNavegacion')
    

    <form action="{{ route('planes.store') }}" method="POST" class="formulario">
        @csrf

        <!-- Nombre del plan -->
        <div class="form-group">
            <label for="nombrePlan">Nombre del plan de pago:</label>
            <input type="text" id="nombrePlan" name="nombrePlan" class="input-grande" required>
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
                            value="0"
                            min="0"
                            step="1">

                    </div>
                @endif
            @endforeach

        </div>

        <!-- Botones Guardar / Cancelar -->
        <div class="form-group">
            <button type="submit" class="btn-boton-formulario">Guardar</button>
            <a href="{{ route('apartadoPlanDePago') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>
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
