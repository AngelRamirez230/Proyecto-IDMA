<?php

namespace App\Services;

use App\Models\Estudiante;
use App\Models\Pago;
use Carbon\Carbon;

class EstadoDeCuentaService
{
    public function generarEstadoDeCuenta(int $idEstudiante)
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
            ->whereIn('idEstatus', [10, 11, 12])
            ->orderBy('fechaLimiteDePago')
            ->get()
            ->groupBy('idCicloModalidad');

        $estadoCuentaPorCiclo = [];

        foreach ($pagosPorCiclo as $idCiclo => $pagos) {

            $cicloModalidad = $pagos->first()->cicloModalidad;

            $pagosEstadoCuenta = $pagos->whereIn('idConceptoDePago', $conceptosEstadoCuenta);

            $otrosPagos = $pagos->filter(function ($p) use ($conceptosValidos, $conceptosEstadoCuenta) {

                if (!in_array($p->idConceptoDePago, $conceptosEstadoCuenta)) {
                    return true;
                }

                if (
                    in_array($p->idConceptoDePago, $conceptosValidos) &&
                    $p->fechaLimiteDePago &&
                    Carbon::parse($p->fechaLimiteDePago)->day != 15
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
                    && Carbon::parse($p->fechaLimiteDePago)->day == 15;
            });

            $importeTotal = $pagosValidos->sum(fn ($p) => $p->costoConceptoOriginal ?? 0);

            $becasTotal = $pagosValidos->sum(fn ($p) => $p->descuentoDeBeca ?? 0);

            $descuentosTotal = $pagosValidos->sum(fn ($p) => $p->descuentoDePago ?? 0);

            $saldoAPagar = max($importeTotal - $becasTotal - $descuentosTotal, 0);

            $abonosASaldo = $pagos->sum(function ($p) use ($conceptosValidos) {

                if ($p->idEstatus != 11) {
                    return 0;
                }

                if ($p->referenciaOriginal && $p->pagoOriginal) {

                    if (
                        $p->pagoOriginal->fechaLimiteDePago &&
                        Carbon::parse($p->pagoOriginal->fechaLimiteDePago)->day == 15
                    ) {
                        return $p->pagoOriginal->montoAPagar ?? 0;
                    }

                    return 0;
                }

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

                if (in_array($p->idConceptoDePago, $conceptosValidos)) {
                    return $p->fechaLimiteDePago
                        && Carbon::parse($p->fechaLimiteDePago)->day == 15;
                }

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
}