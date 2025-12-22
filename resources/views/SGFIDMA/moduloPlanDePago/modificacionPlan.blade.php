<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Plan de Pago</title>
    @vite(['resources/css/app.css', 'resources/css/plan.css'])
</head>
<body>

@include('layouts.barraNavegacion')

<form action="{{ route('planes.update', $plan->idPlanDePago) }}" method="POST" class="formulario">
    @csrf
    @method('PUT')

    <!-- Nombre del plan -->
    <div class="form-group">
        <label for="nombrePlan">Nombre del plan de pago:</label>
        <input type="text" id="nombrePlan" name="nombrePlan" class="input-grande input-bloqueado" value="{{ $plan->nombrePlanDePago }}" readonly>
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

    <div class="form-group">

        <button type="submit" name="accion" value="guardar" class="btn-boton-formulario">Guardar cambios</button>

        <button type="submit"
                    name="accion"
                    value="Suspender/Habilitar"
                    class="btn-boton-formulario">
                {{ $plan->idEstatus == 1 ? 'Suspender' : 'Habilitar' }}
            </button>

        <a href="{{ route('consultaPlan') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
    </div>

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
