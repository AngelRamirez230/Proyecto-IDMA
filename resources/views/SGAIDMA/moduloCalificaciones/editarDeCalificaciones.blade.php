<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar calificaciones</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Editar calificaciones</h1>

        <section class="consulta-controles">
            <div class="consulta-busqueda-group" style="max-width: 520px;">
                <input type="text" value="Grupo: {{ $horario->claveGrupo ?? 'Sin grupo' }} | Asignatura: {{ $horario->asignatura ?? 'Sin asignatura' }}" readonly>
            </div>
        </section>

        <section class="consulta-controles">
            <div class="consulta-selects">
                <select id="selectorPeriodo" class="select select-boton">
                    <option value="parcial1">Parcial 1</option>
                    <option value="parcial2">Parcial 2</option>
                    <option value="ordinario">Ordinario</option>
                    <option value="extraordinario">Extraordinario</option>
                    <option value="titulo">Título de suficiencia</option>
                    <option value="finales">Calificaciones finales</option>
                </select>
            </div>
        </section>

        <form action="#" method="POST" id="formCalificaciones">
            @csrf

            <section class="consulta-tabla-contenedor periodo-seccion" data-periodo="parcial1">
                <h2 class="consulta-titulo" style="font-size: 1.1rem;">Parcial 1</h2>
                @include('SGAIDMA.moduloCalificaciones.partials.tablaCalificaciones', [
                    'periodo' => 'parcial1',
                    'estudiantes' => $estudiantes
                ])
            </section>

            <section class="consulta-tabla-contenedor periodo-seccion" data-periodo="parcial2" style="display:none;">
                <h2 class="consulta-titulo" style="font-size: 1.1rem;">Parcial 2</h2>
                @include('SGAIDMA.moduloCalificaciones.partials.tablaCalificaciones', [
                    'periodo' => 'parcial2',
                    'estudiantes' => $estudiantes
                ])
            </section>

            <section class="consulta-tabla-contenedor periodo-seccion" data-periodo="ordinario" style="display:none;">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <h2 class="consulta-titulo" style="font-size: 1.1rem;">Ordinario</h2>
                    <button type="button" class="btn-boton" id="btnAplicarExentos">Aplicar exentos</button>
                </div>
                @include('SGAIDMA.moduloCalificaciones.partials.tablaCalificaciones', [
                    'periodo' => 'ordinario',
                    'estudiantes' => $estudiantes
                ])
            </section>

            <section class="consulta-tabla-contenedor periodo-seccion" data-periodo="extraordinario" style="display:none;">
                <h2 class="consulta-titulo" style="font-size: 1.1rem;">Extraordinario</h2>
                <div id="mensajeExtraordinario" class="tablaVacia" style="display:none;">
                    Sin estudiantes en situacion de Extraordinario
                </div>
                @include('SGAIDMA.moduloCalificaciones.partials.tablaCalificaciones', [
                    'periodo' => 'extraordinario',
                    'estudiantes' => $estudiantes
                ])
            </section>

            <section class="consulta-tabla-contenedor periodo-seccion" data-periodo="titulo" style="display:none;">
                <h2 class="consulta-titulo" style="font-size: 1.1rem;">Título de suficiencia</h2>
                <div id="mensajeTitulo" class="tablaVacia" style="display:none;">
                    Sin estudiantes en situacion de Titulo de suficiencia
                </div>
                @include('SGAIDMA.moduloCalificaciones.partials.tablaCalificaciones', [
                    'periodo' => 'titulo',
                    'estudiantes' => $estudiantes
                ])
            </section>

            <section class="consulta-tabla-contenedor periodo-seccion" data-periodo="finales" style="display:none;">
                <h2 class="consulta-titulo" style="font-size: 1.1rem;">Calificaciones finales</h2>
                <table class="tabla">
                    <thead>
                        <tr class="tabla-encabezado">
                            <th>Estudiante</th>
                            <th>Parcial 1</th>
                            <th>Parcial 2</th>
                            <th>Ordinario</th>
                            <th>Final</th>
                        </tr>
                    </thead>
                    <tbody class="tabla-cuerpo" id="tablaFinales">
                        @foreach ($estudiantes as $estudiante)
                            <tr class="tabla-fila" data-estudiante="{{ $estudiante->idEstudiante }}">
                                <td>{{ $estudiante->nombre ?? 'Sin nombre' }}</td>
                                <td data-final-parcial1>0</td>
                                <td data-final-parcial2>0</td>
                                <td data-final-ordinario>0</td>
                                <td data-final-total>0</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <div style="margin-top: 16px;">
                <button type="submit" class="btn-boton">Guardar calificaciones</button>
            </div>
        </form>
    </main>

    <script>
        const selectorPeriodo = document.getElementById('selectorPeriodo');
        const secciones = document.querySelectorAll('.periodo-seccion');
        const btnExentos = document.getElementById('btnAplicarExentos');

        selectorPeriodo.addEventListener('change', () => {
            const valor = selectorPeriodo.value;
            secciones.forEach((seccion) => {
                seccion.style.display = seccion.dataset.periodo === valor ? '' : 'none';
            });
        });

        const clamp = (value) => {
            if (Number.isNaN(value)) return null;
            return Math.min(10, Math.max(0, value));
        };

        const redondearCalificacion = (suma) => {
            if (suma === 0) return 0;
            if (suma > 0 && suma < 6) return 5;
            if (suma >= 6 && suma <= 10) return Math.round(suma);
            return 10;
        };

        const calcularFila = (fila) => {
            const inputs = fila.querySelectorAll('input[data-criterio]');
            let total = 0;
            let invalido = false;

            inputs.forEach((input) => {
                const valor = clamp(parseFloat(input.value || '0'));
                if (valor === null) {
                    invalido = true;
                    return;
                }
                if (valor < 0 || valor > 10) {
                    invalido = true;
                }
                total += valor;
            });

            const campoTotal = fila.querySelector('[data-total]');
            if (campoTotal) {
                const resultado = invalido ? 0 : redondearCalificacion(total);
                campoTotal.value = resultado;
                campoTotal.dataset.raw = total.toFixed(1);
            }
        };

        const calcularFinal = () => {
            const filasFinales = document.querySelectorAll('#tablaFinales tr[data-estudiante]');
            filasFinales.forEach((filaFinal) => {
                const id = filaFinal.dataset.estudiante;
                const parcial1 = obtenerTotalPeriodo('parcial1', id);
                const parcial2 = obtenerTotalPeriodo('parcial2', id);
                const ordinario = obtenerTotalPeriodo('ordinario', id);

                filaFinal.querySelector('[data-final-parcial1]').textContent = parcial1;
                filaFinal.querySelector('[data-final-parcial2]').textContent = parcial2;
                filaFinal.querySelector('[data-final-ordinario]').textContent = ordinario;

                let final = 0;
                if (parcial1 > 0 && parcial2 > 0 && ordinario > 0) {
                    final = (((parcial1 + parcial2) / 2) + ordinario) / 2;
                    final = Math.round(final * 10) / 10;
                }
                filaFinal.querySelector('[data-final-total]').textContent = final;
            });

            actualizarExtraordinario();
        };

        const obtenerTotalPeriodo = (periodo, idEstudiante) => {
            const fila = document.querySelector(`[data-periodo="${periodo}"] tr[data-estudiante="${idEstudiante}"]`);
            if (!fila) return 0;
            const campoTotal = fila.querySelector('[data-total]');
            return campoTotal ? parseFloat(campoTotal.value || '0') : 0;
        };

        const actualizarExtraordinario = () => {
            const filasExtra = document.querySelectorAll('[data-periodo="extraordinario"] tr[data-estudiante]');
            let visiblesExtra = 0;

            filasExtra.forEach((fila) => {
                const id = fila.dataset.estudiante;
                const parcial1 = obtenerTotalPeriodo('parcial1', id);
                const parcial2 = obtenerTotalPeriodo('parcial2', id);
                const ordinario = obtenerTotalPeriodo('ordinario', id);
                const final = (((parcial1 + parcial2) / 2) + ordinario) / 2;
                const mostrar = (final < 6) || parcial1 === 0 || parcial2 === 0 || ordinario === 0;

                fila.style.display = mostrar ? '' : 'none';
                if (mostrar) visiblesExtra += 1;
            });

            const mensajeExtra = document.getElementById('mensajeExtraordinario');
            if (mensajeExtra) {
                mensajeExtra.style.display = visiblesExtra === 0 ? '' : 'none';
            }

            const filasTitulo = document.querySelectorAll('[data-periodo="titulo"] tr[data-estudiante]');
            let visiblesTitulo = 0;

            filasTitulo.forEach((fila) => {
                const id = fila.dataset.estudiante;
                const extraordinario = obtenerTotalPeriodo('extraordinario', id);
                const mostrar = extraordinario > 0 && extraordinario < 6;
                fila.style.display = mostrar ? '' : 'none';
                if (mostrar) visiblesTitulo += 1;
            });

            const mensajeTitulo = document.getElementById('mensajeTitulo');
            if (mensajeTitulo) {
                mensajeTitulo.style.display = visiblesTitulo === 0 ? '' : 'none';
            }
        };

        document.querySelectorAll('table.tabla').forEach((tabla) => {
            tabla.addEventListener('input', (event) => {
                const fila = event.target.closest('tr[data-estudiante]');
                if (!fila) return;
                calcularFila(fila);
                calcularFinal();
            });
        });

        if (btnExentos) {
            btnExentos.addEventListener('click', () => {
                document.querySelectorAll('[data-periodo="ordinario"] tr[data-estudiante]').forEach((fila) => {
                    const id = fila.dataset.estudiante;
                    const parcial1 = obtenerTotalPeriodo('parcial1', id);
                    const parcial2 = obtenerTotalPeriodo('parcial2', id);
                    const promedio = (parcial1 + parcial2) / 2;
                    if (promedio >= 9) {
                        const total = fila.querySelector('[data-total]');
                        if (total) {
                            total.value = Math.round(promedio * 10) / 10;
                        }
                    }
                });
                calcularFinal();
            });
        }

        document.querySelectorAll('tr[data-estudiante]').forEach((fila) => calcularFila(fila));
        calcularFinal();
    </script>
</body>
</html>
