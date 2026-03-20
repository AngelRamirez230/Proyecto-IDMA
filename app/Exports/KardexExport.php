<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use App\Http\Controllers\ReporteFinancieroController;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class KardexExport implements FromArray, WithCustomStartCell, WithColumnWidths, WithStyles
{
    protected $estudiante_id;

    public function __construct($estudiante_id)
    {
        $this->estudiante_id = $estudiante_id;
    }

    
    public function startCell(): string
    {
        return 'A8';
    }


    public function columnWidths(): array
    {
        return [
            'A' => 5,   // margen izquierdo
            'B' => 25,  // Concepto
            'C' => 25,  // Semestre o mes
            'D' => 15,  // Cantidad
            'E' => 18,  // Monto
            'F' => 18,  // Fecha
            'G' => 25,  // Forma de pago
            'H' => 5,
            'I' => 5,
            'J' => 18,  // Saldo
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 🔹 Obtener datos otra vez
        $controller = new ReporteFinancieroController();
        $data = $controller->generarKardex($this->estudiante_id);
        $kardex = $data['kardex'];

        $sheet->mergeCells('B8:J8');
        $sheet->getStyle('B8')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('C9:J9');
        $sheet->getStyle('C9')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('C10:J10');
        $sheet->getStyle('C10')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('E11:J11');
        $sheet->getStyle('E11')->getAlignment()->setHorizontal('center');

        
            
        $filaInicio = 12;
        $ultimaFila = $filaInicio + count($kardex);

        for ($fila = $filaInicio; $fila <= $ultimaFila; $fila++) {

            $sheet->mergeCells("G{$fila}:I{$fila}");

            $sheet->getStyle("G{$fila}:I{$fila}")->getAlignment()
                ->setHorizontal('center')
                ->setVertical('center');
        }


        return [];
    }

    public function array(): array
    {
        $controller = new ReporteFinancieroController();
        $data = $controller->generarKardex($this->estudiante_id);

        $kardex = $data['kardex'];
        $resumen = $data['resumen'];
        $totalPagado = $data['totalPagado'];
        $totalCantidad = $data['totalCantidad'];
        $estudiante = $data['estudiante'];

        $saldo = 0;
        $filas = [];

        // =========================
        // TITULO
        // =========================
        $filas[] = ['', 'KARDEX DE PAGOS'];
        $filas[] = [];

        // =========================
        // ENCABEZADOS
        // =========================
        $nombre = strtoupper(trim(
            ($estudiante->usuario->primerNombre ?? '') . ' ' .
            ($estudiante->usuario->segundoNombre ?? '') . ' ' .
            ($estudiante->usuario->primerApellido ?? '') . ' ' .
            ($estudiante->usuario->segundoApellido ?? '')
        ));

        $filas[] = ['', 'Nombre:', $nombre];

        $filas[] = [
            '',
            'Carrera:',
            mb_strtoupper(
                $estudiante->planDeEstudios->licenciatura->nombreLicenciatura ?? '-',
                'UTF-8'
            )
        ];

        $filas[] = [
            '',
            'Matrícula', $estudiante->matriculaAlfanumerica ?? '-',
            'Generación', $estudiante->generacion->nombreGeneracion ?? '-'
        ];

        $filas[] = [];

        // =========================
        // ENCABEZADO TABLA
        // =========================
        $filas[] = [
            '',
            'Concepto',
            'Semestre o mes',
            'Cantidad',
            'Monto',
            'Fecha',
            'Forma de pago',
            '',
            '',
            'Saldo'
        ];

        // =========================
        // DATOS
        // =========================
        foreach ($kardex as $fila) {

            if (($fila['estado'] ?? null) == 11 && !empty($fila['monto'])) {
                $saldo += $fila['monto'];
            }

            $filas[] = [
                '',
                $fila['concepto'],
                $fila['periodo'],
                ($fila['estado'] == 11) ? $fila['monto'] : '-',
                ($fila['estado'] == 11) ? $fila['monto'] : '-',
                ($fila['estado'] == 11 && !empty($fila['fechaPago']))
                    ? Carbon::parse($fila['fechaPago'])->format('d/m/Y')
                    : '-',
                ($fila['estado'] == 11) ? $fila['formaPago'] : '-',
                '',
                '',
                ($fila['estado'] == 11) ? $saldo : '-',
            ];
        }

        // =========================
        // ESPACIO
        // =========================
        $filas[] = ['', '', '', '', '', '', '', '', '', ''];

        // =========================
        // RESUMEN
        // =========================
        $filas[] = ['', 'Concepto', '#', 'Monto'];

        $filas[] = ['', 'Mensualidad', $resumen['mensualidad']['cantidad'], $resumen['mensualidad']['monto']];
        $filas[] = ['', 'Inscripción', $resumen['inscripcion']['cantidad'], $resumen['inscripcion']['monto']];
        $filas[] = ['', 'Recargo', $resumen['recargo']['cantidad'], $resumen['recargo']['monto']];
        $filas[] = ['', 'Examen', $resumen['examen']['cantidad'], $resumen['examen']['monto']];
        $filas[] = ['', 'Uniforme', $resumen['uniforme']['cantidad'], $resumen['uniforme']['monto']];

        $filas[] = ['', 'Total pagado', $totalCantidad, $totalPagado];

        return $filas;
    }
}