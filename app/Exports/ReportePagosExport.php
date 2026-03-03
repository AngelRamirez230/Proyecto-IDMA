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
                'pendientes' => 10,
                'rechazados' => 12,
                'aprobados'  => 11,
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

        // Número de filas y columnas con datos
        $ultimaFila = $sheet->getHighestRow();
        $ultimaColumna = $sheet->getHighestColumn(); // <- aquí estaba el error

        // Rango de la tabla (A4 hasta H última fila)
        $rango = "A4:H{$ultimaFila}";

        // Bordes para todas las celdas
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle(
            Border::BORDER_THIN
        );

        // Centrar todo
        $sheet->getStyle($rango)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($rango)->getAlignment()->setVertical('center');

        // Filas 1 y 2: letras color #79272C
        $sheet->getStyle('A1:H2')->getFont()->getColor()->setARGB('79272C');
        $sheet->getStyle('A1:H2')->getFont()->setBold(true);

        // Fila 4: encabezados con fondo #79272C y letras blancas
        $sheet->getStyle('A4:H4')->getFont()->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('79272C');

        // ===============================
        // Área de impresión
        // ===============================
        $sheet->getPageSetup()->setPrintArea("A1:{$ultimaColumna}{$ultimaFila}");
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0); // largo automático
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
