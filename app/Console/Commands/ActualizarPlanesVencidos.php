<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EstudiantePlan;
use App\Models\Notificacion;
use Carbon\Carbon;

class ActualizarPlanesVencidos extends Command
{
    protected $signature = 'plan:finalizar';
    protected $description = 'Actualiza los planes de pago vencidos';

    public function handle()
    {
        $hoy = Carbon::today();
        $contador = 0;

        $planes = EstudiantePlan::where('idEstatus', 1)
            ->whereDate('fechaDeFinalizacion', '<', $hoy)
            ->get();

        foreach ($planes as $plan) {

            // Cambiar estatus a vencido
            $plan->update([
                'idEstatus' => 9
            ]);

            $contador++;

            // Notificar al estudiante
            $estudiante = $plan->estudiante()->with('usuario')->first();

            if ($estudiante && $estudiante->usuario) {
                Notificacion::create([
                    'idUsuario'          => $estudiante->usuario->idUsuario,
                    'titulo'             => 'Plan de pago finalizado',
                    'mensaje'            => "Tu plan de pago: {$plan->planDePago->nombrePlanDePago} ha finalizado.",
                    'tipoDeNotificacion' => 2,
                    'fechaDeInicio'      => $hoy->toDateString(),
                    'fechaFin'           => $hoy->copy()->addDays(3)->toDateString(),
                    'leida'              => 0,
                ]);
            }
        }

        $this->info("Planes de pago finalizados: {$contador}");
    }
}
