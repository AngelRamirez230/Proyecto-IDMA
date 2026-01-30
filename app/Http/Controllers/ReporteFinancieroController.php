<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportePagosExport;
use Carbon\Carbon;

class ReporteFinancieroController extends Controller
{
    public function fechas($tipo)
    {
        return view('SGFIDMA.moduloReportesFinanzas.eleccionDeFechas', compact('tipo'));
    }

    public function vistaPrevia(Request $request)
    {
        $tipo = $request->tipo;

        $inicio = \Carbon\Carbon::parse($request->fechaInicioReporte)->startOfDay();
        $fin    = \Carbon\Carbon::parse($request->fechaFinalReporte)->endOfDay();

        $query = Pago::with(['estudiante.usuario', 'concepto', 'estatus']);

        if ($tipo !== 'kardex') {
            $map = [
                'pendientes' => 3,
                'rechazados' => 7,
                'aprobados'  => 6,
            ];
            $query->where('idEstatus', $map[$tipo]);
        }

        $query->whereBetween('fechaGeneracionDePago', [$inicio, $fin]);

        $pagos = $query->get();

        return view('SGFIDMA.moduloReportesFinanzas.vistaPreviaReporte', compact(
            'pagos', 'tipo', 'inicio', 'fin'
        ));
    }


    private function obtenerPagos($request)
    {
        $inicio = \Carbon\Carbon::parse($request->inicio)->startOfDay();
        $fin    = \Carbon\Carbon::parse($request->fin)->endOfDay();

        $query = Pago::with(['estudiante.usuario', 'concepto', 'estatus']);

        if ($request->tipo !== 'kardex') {
            $map = [
                'pendientes' => 3,
                'rechazados' => 7,
                'aprobados'  => 6,
            ];
            $query->where('idEstatus', $map[$request->tipo]);
        }

        return $query
            ->whereBetween('fechaGeneracionDePago', [$inicio, $fin])
            ->get();
    }


    public function exportarPDF(Request $request)
    {
        $pagos = $this->obtenerPagos($request);

        $inicio = Carbon::parse($request->inicio);
        $fin    = Carbon::parse($request->fin);
        $tipo   = $request->tipo;

        $pdf = Pdf::loadView(
            'SGFIDMA.moduloReportesFinanzas.reportePDF',
            compact('pagos', 'inicio', 'fin', 'tipo')
        );

        return $pdf->download('reporte_financiero.pdf');
    }



    public function exportarExcel(Request $request)
    {
        return Excel::download(
            new ReportePagosExport($request),
            'reporte_financiero.xlsx'
        );
    }
}
