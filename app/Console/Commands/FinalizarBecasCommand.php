<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SolicitudDeBeca;
use Carbon\Carbon;

class FinalizarBecasCommand extends Command
{
    /**
     * El nombre y firma del comando
     */
    protected $signature = 'becas:finalizar';

    /**
     * Descripción del comando
     */
    protected $description = 'Finaliza automáticamente las becas cuya fecha de conclusión ya pasó';

    /**
     * Ejecutar el comando
     */
    public function handle()
    {
        $hoy = Carbon::now();

        $becasFinalizadas = SolicitudDeBeca::where('idEstatus', 6) // Aprobadas
            ->whereDate('fechaDeConclusion', '<=', $hoy)
            ->update([
                'idEstatus' => 9 // Finalizada
            ]);

        $this->info("Becas finalizadas: {$becasFinalizadas}");

        return Command::SUCCESS;
    }
}
