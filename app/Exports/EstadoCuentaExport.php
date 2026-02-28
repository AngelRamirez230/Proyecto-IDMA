<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Services\EstadoDeCuentaService;

class EstadoCuentaExport implements FromView
{
    protected $idEstudiante;
    protected $idCiclo;

    public function __construct($idEstudiante, $idCiclo)
    {
        $this->idEstudiante = $idEstudiante;
        $this->idCiclo = $idCiclo;
    }

    public function view(): View
    {
        $service = new EstadoDeCuentaService();
        $data = $service->generarEstadoDeCuenta($this->idEstudiante);

        if (!isset($data['estadoCuentaPorCiclo'][$this->idCiclo])) {
            abort(404, 'Ciclo no encontrado');
        }

        $ciclo = $data['estadoCuentaPorCiclo'][$this->idCiclo];

        return view(
            'SGFIDMA.moduloEstadoDeCuenta.estadoDeCuentaExcel',
            [
                'estudiante' => $data['estudiante'],
                'ciclo'      => $ciclo
            ]
        );
    }

}
