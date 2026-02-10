<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


use App\Models\Estudiante;
use App\Models\Pago;
use App\Models\Usuario;
use Carbon\Carbon;


class EstadoDeCuentaController extends Controller
{

    public function seleccionarEstudiante(Request $request)
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
            // FILTROS
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
                'SGFIDMA.moduloEstadoDeCuenta.seleccionEstudiante',
                [
                    'estudiantes' => $estudiantes,
                    'buscar'      => $buscar,
                    'filtro'      => $filtro,
                    'orden'       => $orden,
                ]
            );

        } catch (\Throwable $e) {

            Log::error('Error al cargar selección de estudiante para estado de cuenta', [
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


    public function vistaPreviaEstadoDeCuenta(Request $request)
    {
        try {

            $request->validate([
                'estudiante_id' => 'required|exists:Estudiante,idEstudiante',
            ]);

            Carbon::setLocale('es');

            // =========================
            // ESTUDIANTE
            // =========================
            $estudiante = Estudiante::with([
                'usuario',
                'planDeEstudios.licenciatura',
                'generacion'
            ])->findOrFail($request->estudiante_id);

            // =========================
            // PAGOS DEL ESTUDIANTE
            // =========================
            $pagos = Pago::with(['concepto', 'estatus', 'tipoDePago'])
                ->where('idEstudiante', $estudiante->idEstudiante)
                ->orderBy('fechaLimiteDePago')
                ->get();

            // =========================
            // CLASIFICACIÓN
            // =========================
            $pagosAprobados  = $pagos->where('idEstatus', 11); // Aprobado
            $pagosPendientes = $pagos->where('idEstatus', 10); // Pendiente
            $pagosNoPagados = $pagos->where('idEstatus', 12);// No pagado

            // =========================
            // IMPORTE TOTAL
            // =========================
            $importeTotal = $pagos
            ->whereIn('idConceptoDePago', [1, 2, 30])
            ->sum(function ($pago) {
                return $pago->concepto->costo ?? 0;
            });


            // =========================
            // BECAS
            // =========================
            $becasTotal = $pagos->sum('descuentoDeBeca');


            // =========================
            // DESCUENTOS
            // =========================
            $descuentosTotal = $pagos->sum('descuentoDePago');


            // =========================
            // SALDO A PAGAR
            // =========================
            $saldoAPagar = max(
                ($importeTotal - $becasTotal - $descuentosTotal),
                0
            );


            // =========================
            // ABONOS A SALDO
            // =========================
            $abonosASaldo = $pagos->sum(function ($pago) {

               $conceptosValidos = [1, 2, 30];

                if (
                    $pago->idEstatus == 11 &&
                    in_array($pago->idConceptoDePago, $conceptosValidos)
                ) {
                    return $pago->montoAPagar ?? 0;
                }

                return 0;
            });


            // =========================
            // ABONO A RECARGOS
            // =========================
            $abonoARecargos = $pagos->sum(function ($pago) {

                // Solo pagos con recargo
                if (
                    $pago->referenciaOriginal &&
                    $pago->idEstatus == 11 &&
                    $pago->pagoOriginal
                ) {
                    $montoDerivado = $pago->montoAPagar ?? 0;
                    $montoOriginal = $pago->pagoOriginal->montoAPagar ?? 0;

                    // El recargo es la diferencia
                    return max($montoDerivado - $montoOriginal, 0);
                }

                return 0;
            });



            // =========================
            // SALDO PENDIENTE
            // =========================
            $saldoPendiente = $pagos->sum(function ($pago) {

                $conceptosValidos = [1, 2, 30];

                if (
                    $pago->idEstatus == 10 &&
                    in_array($pago->idConceptoDePago, $conceptosValidos)
                ) {
                    return $pago->montoAPagar ?? 0;
                }

                return 0;
            });



            // =========================
            // SALDO VENCIDO
            // =========================
            $saldoVencido = $pagos->sum(function ($pago) {

                $conceptosValidos = [1, 2, 30];

                if (
                    $pago->idEstatus == 12 &&
                    in_array($pago->idConceptoDePago, $conceptosValidos)
                ) {
                    return $pago->montoAPagar ?? 0;
                }

                return 0;
            });



            // =========================
            // RECARGOS
            // =========================
            $recargosTotal = $pagos->sum(function ($pago) {

                // El pago debe ser un pago que sustituye a otro
                if (
                    $pago->referenciaOriginal &&
                    $pago->pagoOriginal
                ) {

                    $montoNuevo    = $pago->montoAPagar ?? 0;
                    $montoOriginal = $pago->pagoOriginal->montoAPagar ?? 0;

                    // El recargo es solo la diferencia
                    return max($montoNuevo - $montoOriginal, 0);
                }

                return 0;
            });



            // =========================
            // SALDO ACTUAL
            // =========================
            $saldoActual = max(
                ($saldoAPagar - $abonosASaldo + $recargosTotal),
                0
            );





            return view(
                'SGFIDMA.moduloEstadoDeCuenta.generarEstadoDeCuenta',
                [
                    'estudiante'      => $estudiante,
                    'pagosAprobados'  => $pagosAprobados,
                    'pagosPendientes' => $pagosPendientes,
                    'pagosNoPagados'  => $pagosNoPagados,
                    'tipo'            => 'estado_cuenta',
                    'inicio'          => $pagos->min('fechaGeneracionDePago'),
                    'fin'             => $pagos->max('fechaGeneracionDePago'),
                    'importeTotal'    => $importeTotal,
                    'becasTotal'        => $becasTotal,
                    'descuentosTotal'   => $descuentosTotal,
                    'saldoAPagar'       => $saldoAPagar,
                    'abonosASaldo'      => $abonosASaldo,
                    'abonoARecargos'    => $abonoARecargos,
                    'saldoPendiente'    => $saldoPendiente,
                    'saldoVencido'      => $saldoVencido,
                    'recargosTotal'     => $recargosTotal,
                    'saldoActual'       => $saldoActual,

                ]
            );

        } catch (\Throwable $e) {

            \Log::error('Error al generar vista previa del estado de cuenta', [
                'estudiante_id' => $request->estudiante_id ?? null,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with(
                'popupError',
                'Ocurrió un error al generar el estado de cuenta.'
            );
        }
    }



    public function miEstadoDeCuenta()
    {
        try {
            

            $usuario = auth()->user();

            if (!$usuario || !$usuario->estudiante) {
                abort(403, 'No eres estudiante');
            }

            // Reutiliza TODA tu lógica existente
            return $this->vistaPreviaEstadoDeCuenta(
                new Request([
                    'estudiante_id' => $usuario->estudiante->idEstudiante
                ])
            );

        } catch (\Throwable $e) {

            Log::error('Error al generar estado de cuenta del estudiante', [
                'idUsuario' => auth()->id(),
                'error'     => $e->getMessage(),
            ]);

            return redirect()->back()->with(
                'popupError',
                'No fue posible generar tu estado de cuenta.'
            );
        }
    }



    private function generarSemestresPorGeneracion($generacion)
    {
        $semestres = [];

        $añoInicio = $generacion->añoDeInicio;
        $añoFin    = $generacion->añoDeFinalizacion;

        for ($year = $añoInicio; $year <= $añoFin; $year++) {

            // Marzo - Agosto
            $semestres[] = [
                'nombre' => "Marzo–Agosto {$year}",
                'inicio' => Carbon::create($year, 3, 1)->startOfDay(),
                'fin'    => Carbon::create($year, 8, 31)->endOfDay(),
            ];

            // Septiembre - Febrero
            $semestres[] = [
                'nombre' => "Septiembre {$year} – Febrero " . ($year + 1),
                'inicio' => Carbon::create($year, 9, 1)->startOfDay(),
                'fin'    => Carbon::create($year + 1, 2, 28)->endOfDay(),
            ];
        }

        return $semestres;
    }


}
