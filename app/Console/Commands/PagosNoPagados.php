<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Pago;
use App\Models\ConceptoDePago;
use App\Models\Notificacion;
use App\Services\ReferenciaBancariaAztecaService;
use Carbon\Carbon;

class PagosNoPagados extends Command
{
    protected $signature = 'pagos:no-pagados';
    protected $description = 'Marca como NO PAGADOS los pagos vencidos con 3 días de tolerancia';

    public function handle()
    {
        try {

            DB::transaction(function () {

                $fechaLimite = Carbon::today()->subDays(3);

                $pagosVencidos = Pago::where('idEstatus', 10)
                    ->whereNotNull('fechaLimiteDePago')
                    ->whereDate('fechaLimiteDePago', '<', $fechaLimite)
                    ->get();

                if ($pagosVencidos->isEmpty()) {
                    $this->info("No hay pagos vencidos.");
                    return;
                }

                $conceptoPorMes = [
                    10 => 22,
                    11 => 23,
                    12 => 28,
                    3  => 29,
                    1  => 31,
                    2  => 32,
                    4  => 33,
                    5  => 34,
                    6  => 35,
                    7  => 36,
                    8  => 37,
                    9  => 19,
                ];

                foreach ($pagosVencidos as $pago) {

                    $pago->update(['idEstatus' => 12]);

                    if ($pago->idConceptoDePago != 2) {
                        continue;
                    }

                    $mesNumero = Carbon::parse($pago->fechaLimiteDePago)->month;

                    if (!isset($conceptoPorMes[$mesNumero])) {
                        continue;
                    }

                    $mesNombre = strtoupper(
                        Carbon::create()
                            ->month($mesNumero)
                            ->locale('es')
                            ->translatedFormat('F')
                    );

                    $nuevoConceptoId = $conceptoPorMes[$mesNumero];

                    $yaExiste = Pago::where('referenciaOriginal', $pago->Referencia)
                        ->where('idEstatus', 10)
                        ->exists();

                    if ($yaExiste) {
                        continue;
                    }

                    $estudiante = $pago->estudiante;
                    $concepto   = ConceptoDePago::find($nuevoConceptoId);

                    if (!$estudiante || !$concepto) {
                        continue;
                    }

                    $nuevaFechaLimite = Carbon::today()->addDays(8);

                    // HEREDAR BECA
                    $costoOriginal   = $concepto->costo;
                    $costoFinal      = $costoOriginal;

                    $nombreBeca      = $pago->nombreBeca ?? null;
                    $porcentajeBeca  = $pago->porcentajeDeDescuento ?? 0;
                    $descuentoBeca   = $pago->descuentoDeBeca ?? 0;

                    if ($descuentoBeca > 0) {
                        $costoFinal -= $descuentoBeca;
                        $costoFinal = max($costoFinal, 0);
                    }

                    $referenciaNueva = ReferenciaBancariaAztecaService::generar(
                        $estudiante,
                        $concepto,
                        $costoFinal,
                        $nuevaFechaLimite
                    );

                    Pago::create([
                        'Referencia'               => $referenciaNueva,
                        'idEstudiante'             => $estudiante->idEstudiante,
                        'idConceptoDePago'         => $nuevoConceptoId,
                        'idCicloModalidad'         => $pago->idCicloModalidad,
                        'costoConceptoOriginal'    => $costoOriginal,
                        'nombreBeca'               => $nombreBeca,
                        'porcentajeDeDescuento'    => $porcentajeBeca,
                        'descuentoDeBeca'          => $descuentoBeca,
                        'montoAPagar'              => $costoFinal,
                        'fechaGeneracionDePago'    => Carbon::today(),
                        'fechaLimiteDePago'        => $nuevaFechaLimite,
                        'aportacion'               => "COLEGIATURA CON RECARGO DEL MES DE {$mesNombre}",
                        'idEstatus'                => 10,
                        'referenciaOriginal'       => $pago->Referencia,
                    ]);

                    Notificacion::create([
                        'idUsuario'          => $estudiante->idUsuario,
                        'titulo'             => 'Mensualidad vencida',
                        'mensaje'            => "Tu mensualidad del mes de {$mesNombre} ha vencido.
                                                Se te ha asignado el concepto {$concepto->nombreConceptoDePago}.
                                                Revisa tus pagos.",
                        'tipoDeNotificacion' => 1,
                        'fechaDeInicio'      => Carbon::today()->toDateString(),
                        'fechaFin'           => Carbon::today()->copy()->addDays(5)->toDateString(),
                        'leida'              => 0,
                    ]);
                }

                $this->info("Proceso ejecutado correctamente.");
            });

        } catch (\Exception $e) {

            Log::error('Error en comando pagos:no-pagados', [
                'mensaje' => $e->getMessage(),
                'linea'   => $e->getLine(),
                'archivo' => $e->getFile(),
            ]);

            $this->error("Ocurrió un error. Revisa el log.");
        }
    }
}