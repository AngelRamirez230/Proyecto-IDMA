<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SolicitudDeBeca;
use App\Models\Notificacion;
use Carbon\Carbon;

class FinalizarBecasCommand extends Command
{
    protected $signature = 'becas:finalizar';

    protected $description = 'Finaliza automáticamente las becas vencidas y notifica al estudiante';

    public function handle()
    {
        $hoy = Carbon::today();
        $contador = 0;

        // Cargar relaciones necesarias
        $becas = SolicitudDeBeca::with([
                'estudiante.usuario',
                'beca'
            ])
            ->where('idEstatus', 6) // Aprobadas
            ->whereDate('fechaDeConclusion', '<', $hoy)
            ->get();

        foreach ($becas as $beca) {

            // Cambiar estatus a finalizada
            $beca->update([
                'idEstatus' => 9
            ]);

            $contador++;

            $estudiante = $beca->estudiante;
            $usuario    = $estudiante?->usuario;
            $nombreBeca = $beca->beca?->nombreDeBeca ?? 'tu beca';

            if ($usuario) {
                Notificacion::create([
                    'idUsuario'          => $usuario->idUsuario,
                    'titulo'             => 'Beca finalizada',
                    'mensaje'            => "Tu beca \"{$nombreBeca}\" ha finalizado el {$beca->fechaDeConclusion->format('d/m/Y')}.",
                    'tipoDeNotificacion' => 2, // Advertencia
                    'fechaDeInicio'      => $hoy->toDateString(),
                    'fechaFin'           => $hoy->copy()->addDays(3)->toDateString(),
                    'leida'              => 0,
                ]);
            }
        }

        $this->info("Becas finalizadas: {$contador}");

        return Command::SUCCESS;
    }
}
