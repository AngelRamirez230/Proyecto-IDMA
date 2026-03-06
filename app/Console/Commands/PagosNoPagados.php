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
    protected $description = 'Marca pagos vencidos y genera recargos automáticamente';

    public function handle()
    {
        try {

            DB::transaction(function () {

                $fechaHoy = Carbon::today();

                $pagosVencidos = Pago::where('idEstatus', 10)
                    ->whereNotNull('fechaLimiteDePago')
                    ->whereDate('fechaLimiteDePago', '<', $fechaHoy)
                    ->lockForUpdate()
                    ->get();

                if ($pagosVencidos->isEmpty()) {
                    $this->info("No hay pagos vencidos.");
                    return;
                }

                $conceptoPorMes = [
                    10 => 22,
                    11 => 23,
                    12 => 28,
                    1  => 31,
                    2  => 32,
                    3  => 29,
                    4  => 33,
                    5  => 34,
                    6  => 35,
                    7  => 36,
                    8  => 37,
                    9  => 19,
                ];

                foreach ($pagosVencidos as $pago) {

                    // ==============================
                    // SI NO ES COLEGIATURA
                    // ==============================

                    if ($pago->idConceptoDePago != 2 && !$pago->referenciaOriginal) {

                        $pago->update([
                            'idEstatus' => 12
                        ]);

                        continue;
                    }

                    // ==============================
                    // REFERENCIA RAÍZ
                    // ==============================

                    $referenciaRaiz = $pago->referenciaOriginal ?: $pago->Referencia;

                    // ==============================
                    // PAGO RAÍZ
                    // ==============================

                    $pagoRaiz = Pago::where('Referencia', $referenciaRaiz)->first();

                    if (!$pagoRaiz) {

                        $pago->update([
                            'idEstatus' => 12
                        ]);

                        continue;
                    }

                    // ==============================
                    // MES DEL PAGO ORIGINAL
                    // ==============================

                    $mesNumero = Carbon::parse($pagoRaiz->fechaLimiteDePago)->month;

                    if (!isset($conceptoPorMes[$mesNumero])) {

                        $pago->update([
                            'idEstatus' => 12
                        ]);

                        continue;
                    }

                    $mesNombre = strtoupper(
                        Carbon::create()
                            ->month($mesNumero)
                            ->locale('es')
                            ->translatedFormat('F')
                    );

                    // ==============================
                    // VALIDAR SI YA EXISTE RECARGO
                    // ==============================

                    $yaExiste = Pago::where('referenciaOriginal', $referenciaRaiz)
                        ->where('idEstatus', 10)
                        ->whereDate('fechaLimiteDePago', '>=', $fechaHoy)
                        ->where('Referencia', '!=', $pago->Referencia)
                        ->exists();

                    if ($yaExiste) {

                        $pago->update([
                            'idEstatus' => 12
                        ]);

                        continue;
                    }

                    $concepto = ConceptoDePago::find($conceptoPorMes[$mesNumero]);
                    $estudiante = $pago->estudiante;

                    if (!$concepto || !$estudiante) {

                        $pago->update([
                            'idEstatus' => 12
                        ]);

                        continue;
                    }

                    // ==============================
                    // MARCAR COMO NO PAGADO
                    // ==============================

                    $pago->update([
                        'idEstatus' => 12
                    ]);

                    // ==============================
                    // COSTO FINAL
                    // ==============================

                    $costoFinal = max(
                        $concepto->costo - ($pago->descuentoDeBeca ?? 0),
                        0
                    );

                    $nuevaFechaLimite = Carbon::today()->addDays(8);

                    $referenciaNueva = ReferenciaBancariaAztecaService::generar(
                        $estudiante,
                        $concepto,
                        $costoFinal,
                        $nuevaFechaLimite
                    );

                    // ==============================
                    // CREAR RECARGO
                    // ==============================

                    Pago::create([
                        'Referencia' => $referenciaNueva,
                        'idEstudiante' => $estudiante->idEstudiante,
                        'idConceptoDePago' => $concepto->idConceptoDePago,
                        'idCicloModalidad' => $pago->idCicloModalidad,
                        'costoConceptoOriginal' => $concepto->costo,
                        'nombreBeca' => $pago->nombreBeca,
                        'porcentajeDeDescuento' => $pago->porcentajeDeDescuento,
                        'descuentoDeBeca' => $pago->descuentoDeBeca,
                        'montoAPagar' => $costoFinal,
                        'fechaGeneracionDePago' => Carbon::today(),
                        'fechaLimiteDePago' => $nuevaFechaLimite,
                        'aportacion' => "COLEGIATURA CON RECARGO DEL MES DE {$mesNombre}",
                        'idEstatus' => 10,
                        'referenciaOriginal' => $referenciaRaiz
                    ]);

                    // ==============================
                    // CREAR NOTIFICACIÓN
                    // ==============================

                    $conceptoVencido = optional($pago->concepto)->nombreConceptoDePago ?? 'Concepto vencido';
                    $conceptoRecargo = $concepto->nombreConceptoDePago ?? 'Concepto con recargo';

                    // Si el pago vencido es el pago raíz
                    if (!$pago->referenciaOriginal) {

                        $titulo = 'Pago de colegiatura vencido';

                        $mensaje = "Tu pago \"{$conceptoVencido}\" correspondiente al mes de {$mesNombre} ha vencido.\n
                    Se ha generado un nuevo pago \"{$conceptoRecargo}\".\n
                    Revisa tus pagos.";

                    } 
                    // Si venció un pago que ya tenía recargo
                    else {

                        $titulo = 'Pago con recargo vencido';

                        $mensaje = "Tu pago con recargo \"{$conceptoVencido}\" correspondiente al mes de {$mesNombre} ha vencido.\n
                    Se ha generado un nuevo pago \"{$conceptoRecargo}\".\n
                    Revisa tus pagos.";

                    }

                    Notificacion::create([
                        'idUsuario' => $estudiante->idUsuario,
                        'titulo' => $titulo,
                        'mensaje' => $mensaje,
                        'tipoDeNotificacion' => 1,
                        'fechaDeInicio' => Carbon::today(),
                        'fechaFin' => Carbon::today()->copy()->addDays(5),
                        'leida' => 0
                    ]);
                }

                $this->info("Proceso ejecutado correctamente.");
            });

        } catch (\Exception $e) {

            Log::error('Error en comando pagos:no-pagados', [
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile(),
            ]);

            $this->error("Ocurrió un error. Revisa el log.");
        }
    }
}