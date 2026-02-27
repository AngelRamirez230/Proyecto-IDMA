<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Http\Controllers\EstadoDeCuentaController;

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
        $controller = new EstadoDeCuentaController();
        $data = $controller->generarEstadoDeCuenta($this->idEstudiante);

        $ciclo = $data['estadoCuentaPorCiclo'][$this->idCiclo];

        return view(
            'SGFIDMA.moduloEstadoDeCuenta.excel.estado-cuenta-excel',
            [
                'estudiante' => $data['estudiante'],
                'ciclo'      => $ciclo
            ]
        );
    }

}
