<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


use App\Models\Estudiante;
use App\Models\CicloModalidad;
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



    private function generarEstadoDeCuenta(int $idEstudiante)
    {
        Carbon::setLocale('es');

        $conceptosValidos = [1, 2, 30];
        $conceptosEstadoCuenta = [1,2,30,19,22,23,28,29,31,32,33,34,35,36,37];


        $estudiante = Estudiante::with([
            'usuario',
            'planDeEstudios.licenciatura',
            'generacion'
        ])->findOrFail($idEstudiante);

        $pagosPorCiclo = Pago::with([
            'concepto',
            'estatus',
            'tipoDePago',
            'cicloModalidad.cicloEscolar',
            'pagoOriginal'
        ])
            ->where('idEstudiante', $idEstudiante)
            ->orderBy('fechaLimiteDePago')
            ->get()
            ->groupBy('idCicloModalidad');

        $estadoCuentaPorCiclo = [];

        foreach ($pagosPorCiclo as $idCiclo => $pagos) {

            $cicloModalidad = $pagos->first()->cicloModalidad;

            $pagosEstadoCuenta = $pagos->whereIn('idConceptoDePago', $conceptosEstadoCuenta);

            $otrosPagos = $pagos->filter(function ($p) use ($conceptosValidos, $conceptosEstadoCuenta) {

                // Si no pertenece al estado de cuenta
                if (!in_array($p->idConceptoDePago, $conceptosEstadoCuenta)) {
                    return true;
                }

                // Si es concepto válido pero NO es día 15
                if (
                    in_array($p->idConceptoDePago, $conceptosValidos) &&
                    $p->fechaLimiteDePago &&
                    $p->fechaLimiteDePago->day != 15
                ) {
                    return true;
                }

                return false;
            });

            $nombreCiclo = $cicloModalidad && $cicloModalidad->cicloEscolar
                ? $cicloModalidad->cicloEscolar->nombreCicloEscolar
                : 'Ciclo sin definir';

            $pagosValidos = $pagos->filter(function ($p) use ($conceptosValidos) {
                return in_array($p->idConceptoDePago, $conceptosValidos)
                    && $p->fechaLimiteDePago
                    && $p->fechaLimiteDePago->day == 15;
            });

            $importeTotal = $pagosValidos
                ->sum(fn ($p) => $p->costoConceptoOriginal ?? 0);

            $becasTotal = $pagosValidos
                ->sum(fn ($p) => $p->descuentoDeBeca ?? 0);

            $descuentosTotal = $pagosValidos
                ->sum(fn ($p) => $p->descuentoDePago ?? 0);

            $saldoAPagar = max($importeTotal - $becasTotal - $descuentosTotal, 0);

            $abonosASaldo = $pagos->sum(function ($p) use ($conceptosValidos) {

                // Solo pagos aplicados a saldo
                if ($p->idEstatus != 11) {
                    return 0;
                }

                // 🔹 Si es abono con referencia original
                if ($p->referenciaOriginal && $p->pagoOriginal) {

                    if (
                        $p->pagoOriginal->fechaLimiteDePago &&
                        Carbon::parse($p->pagoOriginal->fechaLimiteDePago)->day == 15
                    ) {
                        return $p->pagoOriginal->montoAPagar ?? 0;
                    }

                    return 0;
                }

                // 🔹 Si es pago normal
                if (
                    in_array($p->idConceptoDePago, $conceptosValidos) &&
                    $p->fechaLimiteDePago &&
                    Carbon::parse($p->fechaLimiteDePago)->day == 15
                ) {
                    return $p->montoAPagar ?? 0;
                }

                return 0;
            });

            $saldoPendiente = $pagosValidos
                ->where('idEstatus', 10)
                ->sum(fn ($p) => $p->montoAPagar ?? 0);

            $saldoVencido = $pagosValidos
                ->where('idEstatus', 12)
                ->sum(fn ($p) => $p->montoAPagar ?? 0);

            $recargosTotal = Pago::calcularRecargosDesdeColeccion($pagos);

            $abonoARecargos = $pagos->sum(fn ($p) =>
                $p->idEstatus == 11 &&
                $p->referenciaOriginal &&
                $p->pagoOriginal
                    ? max(
                       ($p->montoAPagar ?? 0) - ($p->pagoOriginal->montoAPagar ?? 0),
                        0
                    )
                    : 0
            );

            $saldoActual = max(
                (
                    $importeTotal
                    - $becasTotal
                    - $descuentosTotal
                )
                + $recargosTotal
                - $abonosASaldo
                - $abonoARecargos,
                0
            );

            


            $pagosFiltradosPorDia15 = $pagosEstadoCuenta->filter(function ($p) use ($conceptosValidos) {

                // Si es concepto 1,2,30 → debe ser día 15
                if (in_array($p->idConceptoDePago, $conceptosValidos)) {
                    return $p->fechaLimiteDePago
                        && $p->fechaLimiteDePago->day == 15;
                }

                // Los demás conceptos del estado de cuenta pasan normal
                return true;
            });

            $pagosAprobados  = $pagosFiltradosPorDia15->where('idEstatus', 11);
            $pagosPendientes = $pagosFiltradosPorDia15->where('idEstatus', 10);
            $pagosNoPagados  = $pagosFiltradosPorDia15->where('idEstatus', 12);

            $estadoCuentaPorCiclo[$idCiclo] = [
                'nombreCiclo'      => $nombreCiclo,
                'cicloModalidad'   => $cicloModalidad,

                'pagos'            => $pagosEstadoCuenta,
                'otrosPagos'       => $otrosPagos,

                'pagosAprobados'   => $pagosAprobados,
                'pagosPendientes'  => $pagosPendientes,
                'pagosNoPagados'   => $pagosNoPagados,

                'importeTotal'     => $importeTotal,
                'becasTotal'       => $becasTotal,
                'descuentosTotal'  => $descuentosTotal,
                'saldoAPagar'      => $saldoAPagar,
                'abonosASaldo'     => $abonosASaldo,
                'abonoARecargos'   => $abonoARecargos,
                'saldoPendiente'   => $saldoPendiente,
                'saldoVencido'     => $saldoVencido,
                'recargosTotal'    => $recargosTotal,
                'saldoActual'      => $saldoActual,
            ];



        }

        return compact('estudiante', 'estadoCuentaPorCiclo');
    }



    public function vistaPreviaEstadoDeCuenta(Request $request)
    {
        $request->validate([
            'estudiante_id' => 'required|exists:Estudiante,idEstudiante',
        ]);

        $data = $this->generarEstadoDeCuenta($request->estudiante_id);

        return view(
            'SGFIDMA.moduloEstadoDeCuenta.generarEstadoDeCuenta',
            array_merge($data, [
                'tipo' => 'estado_cuenta'
            ])
        );
    }





    public function miEstadoDeCuenta()
    {
        try {

            $usuario = auth()->user();

            if (!$usuario || !$usuario->estudiante) {
                abort(403, 'No eres estudiante');
            }

            $data = $this->generarEstadoDeCuenta(
                $usuario->estudiante->idEstudiante
            );

            return view(
                'SGFIDMA.moduloEstadoDeCuenta.generarEstadoDeCuenta',
                array_merge($data, [
                    'tipo' => 'mi_estado_cuenta'
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




}
