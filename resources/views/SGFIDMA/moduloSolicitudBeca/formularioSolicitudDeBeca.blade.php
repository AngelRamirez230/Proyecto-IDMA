<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar beca</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <form action="#" method="POST" class="formulario">
    @csrf
    @method('PUT') 

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


        <div class="form-group">
            <label for="nombreBeca">Nombre de Beca:</label>
             <input type="text" id="nombreBeca" name="nombreBeca" class="input-grande input-bloqueado" value="" readonly>
        </div>

        <div class="form-group">
            <label for="porcentajeBeca">Porcentaje de descuento:</label>
            <div class="contenedor-input-icono">
                <input type="text" id="porcentajeBeca" name="porcentajeBeca" class="input-chico input-bloqueado" value="" readonly>
                <img src="{{ asset('imagenes/IconoPorcentaje.png') }}" class="icono-input-img" alt="icono">
            </div>
        </div>

        <div class="form-group">
            <label for="promedio">¿Cuál fue tu promedio en el semestre que acabas de cursar?</label>
             <input type="text" id="promedio" name="promedio" class="input-chico" required>
        </div>

        <div class="form-group">
            <label for="examenExtraordinario">¿En el semestre cursado ¿Presentaste algún examen extraordinario? ¿Cuál?</label>
             <input type="text" id="examenExtraordinario" name="examenExtraordinario" class="input-grande" required>
        </div>
        


        <div class="form-group">
            <button type="submit"  name="accion" value="guardar" class="btn-boton-formulario">Solicitar beca</button>
            <a href="{{ route('consultaBeca') }}" class="btn-boton-formulario btn-cancelar">Cancelar</a>
        </div>
    </form>
    
    
</body>
</html>