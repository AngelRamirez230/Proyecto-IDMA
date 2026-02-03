<?php

namespace App\Exports;

use App\Models\Pago;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ReportePagosExport implements FromView, WithStyles, WithTitle, WithEvents
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $inicio = Carbon::parse($this->request->inicio)->startOfDay();
        $fin    = Carbon::parse($this->request->fin)->endOfDay();
        $tipo   = $this->request->tipo;

        $query = Pago::with(['estudiante.usuario', 'concepto', 'estatus']);

        if ($tipo !== 'kardex') {
            $map = [
                'pendientes' => 3,
                'rechazados' => 7,
                'aprobados'  => 6,
            ];
            $query->where('idEstatus', $map[$tipo]);
        }

        $pagos = $query
            ->whereBetween('fechaGeneracionDePago', [$inicio, $fin])
            ->get();

        return view('SGFIDMA.moduloReportesFinanzas.reporteExcel', compact(
            'pagos', 'inicio', 'fin', 'tipo'
        ));
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('B')->getNumberFormat()
            ->setFormatCode('@');

        // Formato moneda en columna D
        $sheet->getStyle('D')->getNumberFormat()
            ->setFormatCode('"$"#,##0.00');

        // Número de filas con datos
        $ultimaFila = $sheet->getHighestRow();

        // Rango completo (A1 hasta H última fila)
        $rango = "A1:H{$ultimaFila}";

        // Bordes para todas las celdas
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle(
            Border::BORDER_THIN
        );

        // Centrar todo (opcional pero se ve pro)
        $sheet->getStyle($rango)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($rango)->getAlignment()->setVertical('center');
    }


    public function title(): string
    {
        return 'Reporte de pagos ' .($this->request->tipo);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                
                $ultimaFila = $sheet->getHighestRow();

                for ($fila = 5; $fila <= $ultimaFila; $fila++) {
                    $valor = $sheet->getCell("B{$fila}")->getValue();

                    $sheet->setCellValueExplicit(
                        "B{$fila}",
                        (string) $valor,
                        DataType::TYPE_STRING
                    );
                }
            },
        ];
    }


}
