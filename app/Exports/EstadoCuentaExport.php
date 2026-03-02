<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Services\EstadoDeCuentaService;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;



class EstadoCuentaExport implements FromView, WithStyles
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

    public function styles(Worksheet $sheet)
    {
        $ultimaFila = $sheet->getHighestRow();
        $ultimaColumna = $sheet->getHighestColumn();

        /*
        |----------------------------------------------------------------------
        | FORMATO MONEDA
        |----------------------------------------------------------------------
        */
        foreach (['D','E','F','G','H','I'] as $columna) {
            $sheet->getStyle("{$columna}1:{$columna}{$ultimaFila}")
                ->getNumberFormat()
                ->setFormatCode('"$"#,##0.00');
        }

        /*
        |----------------------------------------------------------------------
        | MAPEAR CELDAS COMBINADAS
        |----------------------------------------------------------------------
        */
        $mergeMap = [];
        foreach ($sheet->getMergeCells() as $mergeRange) {
            $cells = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::extractAllCellReferencesInRange($mergeRange);
            foreach ($cells as $cell) {
                $mergeMap[$cell] = $mergeRange;
            }
        }

        /*
        |----------------------------------------------------------------------
        | BORDES INTELIGENTES (SOPORTA MERGES)
        |----------------------------------------------------------------------
        */
        $procesados = [];
        for ($fila = 3; $fila <= $ultimaFila; $fila++) {
            foreach (range('A', $ultimaColumna) as $col) {
                $coordenada = "{$col}{$fila}";
                $valor = $sheet->getCell($coordenada)->getValue();

                if ($valor === null) continue;

                if (isset($mergeMap[$coordenada])) {
                    $mergeRange = $mergeMap[$coordenada];
                    if (!isset($procesados[$mergeRange])) {
                        $sheet->getStyle($mergeRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                        $procesados[$mergeRange] = true;
                    }
                } else {
                    $sheet->getStyle($coordenada)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }
            }
        }

        /*
        |----------------------------------------------------------------------
        | ALINEACIONES
        |----------------------------------------------------------------------
        */
        $sheet->getStyle("A1:I{$ultimaFila}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle("H6:H15")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("I6:I15")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // ESTILOS DE COLORES DE LETRA Y DE ENCABEZADOS 
        $sheet->getStyle("A1:I1")->getFont()->getColor()->setARGB('79272C'); 
        $sheet->getStyle("A2:I2")->getFont()->getColor()->setARGB('79272C');

        /*
        |----------------------------------------------------------------------
        | COLORES DE FILAS GENERALES DEL ESTUDIANTE (A5:D5 y H5:I5)
        |----------------------------------------------------------------------
        */
        $sheet->getStyle("A5:D5")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('79272C');
        $sheet->getStyle("H5:I5")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('79272C');

        $sheet->getStyle("A5:D5")->getFont()->getColor()->setARGB('FFFFFF');
        $sheet->getStyle("H5:I5")->getFont()->getColor()->setARGB('FFFFFF');

        /*
        |----------------------------------------------------------------------
        | TITULOS DE TABLAS Y ENCABEZADOS + FILAS DE DATOS ALTERNA
        |----------------------------------------------------------------------
        */
        $titulos = ['PAGOS NO PAGADOS', 'PAGOS PENDIENTES', 'PAGOS APROBADOS', 'OTROS PAGOS'];
        $fila = 1;

        while ($fila <= $ultimaFila) {
            $valor = $sheet->getCell("A{$fila}")->getValue();

            if (in_array($valor, $titulos)) {
                // --- Fila del título ---
                $sheet->getStyle("A{$fila}:I{$fila}")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('79272C');

                $sheet->getStyle("A{$fila}:I{$fila}")
                    ->getFont()->getColor()->setARGB('FFFFFF');

                // --- Fila del encabezado ---
                $encabezadoFila = $fila + 1;
                $sheet->getStyle("A{$encabezadoFila}:I{$encabezadoFila}")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('79272C');

                $sheet->getStyle("A{$encabezadoFila}:I{$encabezadoFila}")
                    ->getFont()->getColor()->setARGB('FFFFFF');

                // --- Filas de datos alternadas ---
                $filaDatos = $encabezadoFila + 1;
                $contador = 0; // reinicia alternancia al empezar tabla

                while ($filaDatos <= $ultimaFila) {
                    $valorFila = $sheet->getCell("A{$filaDatos}")->getValue();

                    // Si encontramos otro título de tabla, salimos del bucle
                    if (in_array($valorFila, $titulos)) break;

                    // Ignorar filas vacías o separadores
                    if ($valorFila !== null && trim($valorFila) !== '') {
                        $color = ($contador % 2 == 0) ? 'FFFFFF' : 'F2F2F2';
                        $sheet->getStyle("A{$filaDatos}:I{$filaDatos}")
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($color);

                        $contador++;
                    }

                    $filaDatos++;
                }

                // Continuar desde la fila siguiente
                $fila = $filaDatos;
            } else {
                $fila++;
            }
        }
    }


}
