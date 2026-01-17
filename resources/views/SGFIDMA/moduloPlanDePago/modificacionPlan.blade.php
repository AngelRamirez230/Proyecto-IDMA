<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Plan de Pago</title>
    @vite(['resources/css/app.css'])
</head>
<body>

    @include('layouts.barraNavegacion')

    <form action="{{ route('planes.update', $plan->idPlanDePago) }}" method="POST" class="formulario2">
        @csrf
        @method('PUT')

        <h1 class="titulo-form2">Modificación de plan de pago</h1>

        {{-- NOMBRE DEL PLAN DE PAGO (SOLO LECTURA) --}}
        <div class="form-group2">
            <label for="nombrePlan">Nombre del plan de pago:</label>
            <input
                type="text"
                id="nombrePlan"
                name="nombrePlan"
                class="input-grande2 input-bloqueado2"
                value="{{ old('nombrePlan', $plan->nombrePlanDePago) }}"
                readonly
            >
            <x-error-field field="nombrePlan" />
        </div>

        <h1 class="selecciona-conceptos">Modifica los conceptos:</h1>

        <div class="lista-conceptos">

            @foreach ($conceptos as $concepto)
                <div class="concepto-item">

                    <!-- Botón menos -->
                    <button type="button"
                            class="btn-cantidad"
                            onclick="cambiarCantidad('{{ $concepto->idConceptoDePago }}', -1)">
                        <img src="{{ asset('imagenes/iconoMenos.png') }}" class="iconos">
                    </button>

                    <!-- Nombre -->
                    <span class="nombre-concepto">{{ $concepto->nombreConceptoDePago }}</span>

                    <!-- Botón más -->
                    <button type="button"
                            class="btn-cantidad"
                            onclick="cambiarCantidad('{{ $concepto->idConceptoDePago }}', 1)">
                        <img src="{{ asset('imagenes/iconoMas.png') }}" class="iconos">
                    </button>

                    <!-- Cantidad -->
                    <input type="number"
                        name="cantidades[{{ $concepto->idConceptoDePago }}]"
                        id="cantidad_{{ $concepto->idConceptoDePago }}"
                        class="cantidad-input"
                        value="{{ $cantidadesActuales[$concepto->idConceptoDePago] ?? 0 }}"
                        min="0"
                        step="1">
                </div>
            @endforeach

        </div>

        <div class="form-group2">

            <button type="submit" name="accion" value="guardar" class="btn-boton-formulario2">Guardar cambios</button>

            <button type="submit"
                        name="accion"
                        value="Suspender/Habilitar"
                        class="btn-boton-formulario2">
                    {{ $plan->idEstatus == 1 ? 'Suspender' : 'Habilitar' }}
                </button>

            <a href="{{ route('consultaPlan') }}" class="btn-boton-formulario2 btn-cancelar2">Cancelar</a>
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
