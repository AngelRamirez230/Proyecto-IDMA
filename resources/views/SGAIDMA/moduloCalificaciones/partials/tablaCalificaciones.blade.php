<table class="tabla">
    <thead>
        <tr class="tabla-encabezado">
            <th>Estudiante</th>
            <th>Asistencia</th>
            <th>Participación</th>
            <th>Control semanal</th>
            <th>Evaluación</th>
            <th>Calificación periodo</th>
            <th>Extemporáneo</th>
        </tr>
    </thead>
    <tbody class="tabla-cuerpo">
        @foreach ($estudiantes as $estudiante)
            <tr class="tabla-fila" data-estudiante="{{ $estudiante->idEstudiante }}">
                <td>{{ $estudiante->nombre ?? 'Sin nombre' }}</td>
                <td>
                    <input
                        type="number"
                        step="0.1"
                        min="0"
                        max="10"
                        name="calificaciones[{{ $periodo }}][{{ $estudiante->idEstudiante }}][asistencia]"
                        value="0"
                        data-criterio="asistencia"
                    >
                </td>
                <td>
                    <input
                        type="number"
                        step="0.1"
                        min="0"
                        max="10"
                        name="calificaciones[{{ $periodo }}][{{ $estudiante->idEstudiante }}][participacion]"
                        value="0"
                        data-criterio="participacion"
                    >
                </td>
                <td>
                    <input
                        type="number"
                        step="0.1"
                        min="0"
                        max="10"
                        name="calificaciones[{{ $periodo }}][{{ $estudiante->idEstudiante }}][evidencia]"
                        value="0"
                        data-criterio="evidencia"
                    >
                </td>
                <td>
                    <input
                        type="number"
                        step="0.1"
                        min="0"
                        max="10"
                        name="calificaciones[{{ $periodo }}][{{ $estudiante->idEstudiante }}][evaluacion]"
                        value="0"
                        data-criterio="evaluacion"
                    >
                </td>
                <td>
                    <input
                        type="number"
                        step="0.1"
                        min="0"
                        max="10"
                        name="calificaciones[{{ $periodo }}][{{ $estudiante->idEstudiante }}][total]"
                        value="0"
                        readonly
                        data-total
                    >
                </td>
                <td>
                    <input
                        type="checkbox"
                        name="calificaciones[{{ $periodo }}][{{ $estudiante->idEstudiante }}][extemporaneo]"
                        value="1"
                    >
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
