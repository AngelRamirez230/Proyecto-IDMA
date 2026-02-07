<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\Generacion;
use App\Models\Estudiante;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportePagosExport;
use Carbon\Carbon;

class ReporteFinancieroController extends Controller
{
    public function fechas($tipo)
    {
        try {

            return view(
                'SGFIDMA.moduloReportesFinanzas.eleccionDeFechas',
                compact('tipo')
            );

        } catch (\Throwable $e) {

            \Log::error('Error al cargar la vista de elección de fechas', [
                'tipo'    => $tipo,
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea'   => $e->getLine(),
            ]);

            return redirect()->back()->with(
                'popupError',
                'Ocurrió un error al cargar la selección de fechas.'
            );
        }
    }

    

    public function vistaPrevia(Request $request)
    {
        try {

            $tipo = $request->tipo;

            // =========================
            // KÁRDEX
            // =========================
            if ($tipo === 'kardex') {

                $request->validate([
                    'estudiante_id' => 'required|exists:estudiante,idEstudiante',
                ]);

                Carbon::setLocale('es');

                // 🔹 Estudiante
                $estudiante = Estudiante::with('generacion.mesInicio', 'generacion.mesFin')
                    ->findOrFail($request->estudiante_id);

                $generacion = $estudiante->generacion;

                // 🔹 Fechas de la generación
                $inicio = Carbon::create(
                    $generacion->añoDeInicio,
                    $generacion->mesInicio->idMes,
                    1
                );

                $fin = Carbon::create(
                    $generacion->añoDeFinalizacion,
                    $generacion->mesFin->idMes,
                    1
                )->endOfMonth();

                // 🔹 Todos los pagos del estudiante
                $pagos = Pago::where('idEstudiante', $estudiante->idEstudiante)->get();

                // 🔹 Mensualidades
                $mensualidades = $pagos
                    ->where('idConceptoDePago', 2)
                    ->filter(fn ($p) => $p->fechaLimiteDePago)
                    ->mapWithKeys(function ($pago) {
                        return [
                            $pago->fechaLimiteDePago->format('Y-m') => $pago
                        ];
                    });

                $inscripciones   = $pagos
                    ->where('idConceptoDePago', 1)
                    ->values();
                $reinscripciones = $pagos
                    ->where('idConceptoDePago', 30)
                    ->values();

                // 🔹 Generar meses
                $meses = [];
                $actual = $inicio->copy();

                while ($actual <= $fin) {
                    $meses[] = $actual->copy();
                    $actual->addMonth();
                }

                // 🔹 Armar kárdex
                $kardex = [];
                $semestre = 1;

                foreach (array_chunk($meses, 6) as $bloque) {

                    if ($semestre > 8) break;

                    $pagoSemestre = $semestre === 1
                        ? $inscripciones->first()
                        : $reinscripciones->get($semestre - 2);

                    $estadoSemestre = $pagoSemestre?->idEstatus ?? 10;

                    $kardex[] = [
                        'concepto'  => $semestre === 1 ? 'Inscripción' : 'Reinscripción',
                        'periodo'   => "{$semestre} Semestre",
                        'tipo'      => 'semestre',
                        'estado'    => $estadoSemestre,
                        'monto'     => $pagoSemestre?->montoAPagar,
                        'fechaPago' => $pagoSemestre?->fechaDePago,
                        'formaPago' => ($pagoSemestre?->idTipoDePago === 3)
                            ? 'Transferencia'
                            : $pagoSemestre?->tipoDePago?->nombreTipoDePago,
                    ];

                    foreach ($bloque as $mes) {

                        $claveMes = $mes->format('Y-m');
                        $pago = $mensualidades[$claveMes] ?? null;

                        $estado = 10;

                        if ($pago) {
                            $estado = $pago->idEstatus;
                        } elseif (now()->gt($mes->copy()->day(15))) {
                            $estado = 12;
                        }

                        $kardex[] = [
                            'concepto'  => 'Mensualidad',
                            'periodo'   => ucfirst($mes->translatedFormat('F Y')),
                            'tipo'      => 'mensualidad',
                            'estado'    => $estado,
                            'monto'     => $pago?->montoAPagar,
                            'fechaPago' => $pago?->fechaDePago,
                            'formaPago' => ($pago?->idTipoDePago === 3)
                                ? 'Transferencia'
                                : $pago?->tipoDePago?->nombreTipoDePago,
                        ];
                    }

                    $semestre++;
                }

                // 🔹 Resumen
                $resumen = [
                    'mensualidad' => ['cantidad' => 0, 'monto' => 0],
                    'inscripcion' => ['cantidad' => 0, 'monto' => 0],
                    'recargo'     => ['cantidad' => 0, 'monto' => 0],
                    'examen'      => ['cantidad' => 0, 'monto' => 0],
                    'uniforme'    => ['cantidad' => 0, 'monto' => 0],
                ];

                foreach ($kardex as $fila) {

                    if (($fila['estado'] ?? null) !== 11) continue;

                    switch (strtolower($fila['concepto'])) {

                        case 'mensualidad':
                            $resumen['mensualidad']['cantidad']++;
                            $resumen['mensualidad']['monto'] += $fila['monto'] ?? 0;
                            break;

                        case 'inscripción':
                        case 'reinscripción':
                            $resumen['inscripcion']['cantidad']++;
                            $resumen['inscripcion']['monto'] += $fila['monto'] ?? 0;
                            break;

                        case 'recargo':
                            $resumen['recargo']['cantidad']++;
                            $resumen['recargo']['monto'] += $fila['monto'] ?? 0;
                            break;

                        case 'examen':
                            $resumen['examen']['cantidad']++;
                            $resumen['examen']['monto'] += $fila['monto'] ?? 0;
                            break;

                        case 'uniforme':
                            $resumen['uniforme']['cantidad']++;
                            $resumen['uniforme']['monto'] += $fila['monto'] ?? 0;
                            break;
                    }
                }

                $totalPagado   = collect($resumen)->sum('monto');
                $totalCantidad = collect($resumen)->sum('cantidad');

                return view(
                    'SGFIDMA.moduloReportesFinanzas.vistaPreviaReporte',
                    compact(
                        'kardex',
                        'resumen',
                        'totalPagado',
                        'totalCantidad',
                        'estudiante',
                        'tipo',
                        'inicio',
                        'fin'
                    )
                );
            }

            // =========================
            // OTROS REPORTES
            // =========================
            $inicio = Carbon::parse($request->fechaInicioReporte)->startOfDay();
            $fin    = Carbon::parse($request->fechaFinalReporte)->endOfDay();

            $query = Pago::with(['estudiante.usuario', 'concepto', 'estatus']);

            if ($tipo !== 'kardex') {
                $map = [
                    'pendientes' => 10,
                    'rechazados' => 12,
                    'aprobados'  => 11,
                ];
                $query->where('idEstatus', $map[$tipo]);
            }

            $query->whereBetween('fechaGeneracionDePago', [$inicio, $fin]);

            $pagos = $query->get();

            return view(
                'SGFIDMA.moduloReportesFinanzas.vistaPreviaReporte',
                compact('pagos', 'tipo', 'inicio', 'fin')
            );

        } catch (\Throwable $e) {

            \Log::error('Error al generar la vista previa del reporte', [
                'tipo'    => $request->tipo ?? null,
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea'   => $e->getLine(),
            ]);

            return redirect()->back()->with(
                'popupError',
                'Ocurrió un error al generar la vista previa del reporte.'
            );
        }
    }



    private function obtenerPagos(Request $request)
    {
        try {

            $query = Pago::with(['estudiante.usuario', 'concepto', 'estatus']);

            $inicio = Carbon::parse($request->inicio)->startOfDay();
            $fin    = Carbon::parse($request->fin)->endOfDay();

            $map = [
                'pendientes' => 10,
                'rechazados' => 12,
                'aprobados'  => 11,
            ];

            if (!isset($map[$request->tipo])) {
                throw new \Exception('Tipo de reporte inválido');
            }

            $query->where('idEstatus', $map[$request->tipo])
                ->whereBetween('fechaGeneracionDePago', [$inicio, $fin]);

            return $query->get();

        } catch (\Throwable $e) {

            \Log::error('Error al obtener pagos para reporte', [
                'tipo'    => $request->tipo ?? null,
                'inicio'  => $request->inicio ?? null,
                'fin'     => $request->fin ?? null,
                'mensaje' => $e->getMessage(),
            ]);

            // Devuelve colección vacía para no romper la vista
            return collect();
        }
    }





    public function exportarPDF(Request $request)
    {
        try {

            $pagos = $this->obtenerPagos($request);

            $inicio = Carbon::parse($request->inicio);
            $fin    = Carbon::parse($request->fin);
            $tipo   = $request->tipo;

            $pdf = Pdf::loadView(
                'SGFIDMA.moduloReportesFinanzas.reportePDF',
                compact('pagos', 'inicio', 'fin', 'tipo')
            )->setPaper('A4', 'landscape');

            return $pdf->download('reporte_financiero.pdf');

        } catch (\Throwable $e) {

            \Log::error('Error al exportar reporte financiero en PDF', [
                'inicio'  => $request->inicio ?? null,
                'fin'     => $request->fin ?? null,
                'tipo'    => $request->tipo ?? null,
                'error'   => $e->getMessage(),
            ]);

            return redirect()->back()->with(
                'popupError',
                'Ocurrió un error al generar el reporte en PDF.'
            );
        }
    }




    public function exportarExcel(Request $request)
    {
        try {

            return Excel::download(
                new ReportePagosExport($request),
                'reporte_financiero.xlsx'
            );

        } catch (\Throwable $e) {

            \Log::error('Error al exportar reporte financiero en Excel', [
                'inicio' => $request->inicio ?? null,
                'fin'    => $request->fin ?? null,
                'tipo'   => $request->tipo ?? null,
                'error'  => $e->getMessage(),
            ]);

            return redirect()->back()->with(
                'popupError',
                'Ocurrió un error al generar el reporte en Excel.'
            );
        }
    }



    public function seleccionarEstudianteKardex(Request $request)
    {
        try {

            $buscar = $request->buscar;
            $filtro = $request->filtro;
            $orden  = $request->orden;

            // =============================
            // QUERY BASE
            // =============================
            $query = Estudiante::with([
                'usuario',
                'planDeEstudios.licenciatura'
            ]);

            // =============================
            // BUSCADOR
            // =============================
            if ($request->filled('buscar')) {

                $buscar = trim($buscar);

                $query->where(function ($q) use ($buscar) {
                    $q->whereHas('usuario', function ($u) use ($buscar) {
                        $u->where('primerNombre', 'LIKE', "%{$buscar}%")
                            ->orWhere('segundoNombre', 'LIKE', "%{$buscar}%")
                            ->orWhere('primerApellido', 'LIKE', "%{$buscar}%")
                            ->orWhere('segundoApellido', 'LIKE', "%{$buscar}%")
                            ->orWhereRaw(
                                "REPLACE(
                                    TRIM(
                                        CONCAT(
                                            primerNombre, ' ',
                                            IFNULL(segundoNombre, ''), ' ',
                                            primerApellido, ' ',
                                            IFNULL(segundoApellido, '')
                                        )
                                    ),
                                    '  ', ' '
                                ) LIKE ?",
                                ["%{$buscar}%"]
                            );
                    })
                    ->orWhere('matriculaAlfanumerica', 'LIKE', "%{$buscar}%");
                });
            }

            // =============================
            // FILTRO POR ESTATUS
            // =============================
            if ($filtro === 'nuevoIngreso') {
                $query->where('grado', 1);
            }

            if ($filtro === 'inscritos') {
                $query->where('grado', '>', 1);
            }

            // =============================
            // ORDENAMIENTO
            // =============================
            if ($orden === 'alfabetico') {
                $query->join('usuario', 'usuario.idUsuario', '=', 'estudiante.idUsuario')
                    ->orderBy('usuario.primerNombre')
                    ->orderBy('usuario.primerApellido')
                    ->orderBy('usuario.segundoApellido')
                    ->select('estudiante.*');
            }

            // =============================
            // PAGINACIÓN
            // =============================
            $estudiantes = $query
                ->paginate(10)
                ->withQueryString();

            return view(
                'SGFIDMA.moduloReportesFinanzas.kardexSeleccionEstudiante',
                [
                    'estudiantes' => $estudiantes,
                    'buscar'      => $buscar,
                ]
            );

        } catch (\Throwable $e) {

            \Log::error('Error al cargar selección de estudiante para kárdex', [
                'buscar' => $request->buscar ?? null,
                'filtro' => $request->filtro ?? null,
                'orden'  => $request->orden ?? null,
                'error'  => $e->getMessage(),
            ]);

            return redirect()->back()->with(
                'popupError',
                'Ocurrió un error al cargar la lista de estudiantes.'
            );
        }
    }



}
