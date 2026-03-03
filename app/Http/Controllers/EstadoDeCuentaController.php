<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


use App\Models\Estudiante;
use App\Models\CicloModalidad;
use App\Models\Pago;
use App\Models\Usuario;
use App\Services\EstadoDeCuentaService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EstadoCuentaExport;

class EstadoDeCuentaController extends Controller
{
    

    public function seleccionarEstudiante(Request $request)
    {
        
        try {

            $buscar = $request->buscar;
            $filtro = $request->filtro;
            $orden  = $request->orden;

            // =============================
            // QUERY BASE
            // =============================
            $query = Estudiante::with([
                'usuario',
                'planDeEstudios.licenciatura'
            ]);

            // =============================
            // BUSCADOR
            // =============================
            if ($request->filled('buscar')) {

                $buscar = trim($buscar);

                $query->where(function ($q) use ($buscar) {
                    $q->whereHas('usuario', function ($u) use ($buscar) {
                        $u->where('primerNombre', 'LIKE', "%{$buscar}%")
                          ->orWhere('segundoNombre', 'LIKE', "%{$buscar}%")
                          ->orWhere('primerApellido', 'LIKE', "%{$buscar}%")
                          ->orWhere('segundoApellido', 'LIKE', "%{$buscar}%")
                          ->orWhereRaw(
                              "REPLACE(
                                  TRIM(
                                      CONCAT(
                                          primerNombre, ' ',
                                          IFNULL(segundoNombre, ''), ' ',
                                          primerApellido, ' ',
                                          IFNULL(segundoApellido, '')
                                      )
                                  ),
                                  '  ', ' '
                              ) LIKE ?",
                              ["%{$buscar}%"]
                          );
                    })
                    ->orWhere('matriculaAlfanumerica', 'LIKE', "%{$buscar}%");
                });
            }

            // =============================
            // FILTROS
            // =============================
            if ($filtro === 'nuevoIngreso') {
                $query->where('grado', 1);
            }

            if ($filtro === 'inscritos') {
                $query->where('grado', '>', 1);
            }

            // =============================
            // ORDENAMIENTO
            // =============================
            if ($orden === 'alfabetico') {
                $query->join('usuario', 'usuario.idUsuario', '=', 'estudiante.idUsuario')
                      ->orderBy('usuario.primerNombre')
                      ->orderBy('usuario.primerApellido')
                      ->orderBy('usuario.segundoApellido')
                      ->select('estudiante.*');
            }

            // =============================
            // PAGINACIÓN
            // =============================
            $estudiantes = $query
                ->paginate(10)
                ->withQueryString();

            return view(
                'SGFIDMA.moduloEstadoDeCuenta.seleccionEstudiante',
                [
                    'estudiantes' => $estudiantes,
                    'buscar'      => $buscar,
                    'filtro'      => $filtro,
                    'orden'       => $orden,
                ]
            );

        } catch (\Throwable $e) {

            Log::error('Error al cargar selección de estudiante para estado de cuenta', [
                'buscar' => $request->buscar ?? null,
                'filtro' => $request->filtro ?? null,
                'orden'  => $request->orden ?? null,
                'error'  => $e->getMessage(),
            ]);

            return redirect()->back()->with(
                'popupError',
                'Ocurrió un error al cargar la lista de estudiantes.'
            );
        }
    }



    public function vistaPreviaEstadoDeCuenta(Request $request)
    {
        $request->validate([
            'estudiante_id' => 'required|exists:Estudiante,idEstudiante',
        ]);

        $service = new EstadoDeCuentaService();
        $data = $service->generarEstadoDeCuenta($request->estudiante_id);

        return view(
            'SGFIDMA.moduloEstadoDeCuenta.generarEstadoDeCuenta',
            array_merge($data, [
                'tipo' => 'estado_cuenta'
            ])
        );
    }





    public function miEstadoDeCuenta()
    {
        try {

            $usuario = auth()->user();

            if (!$usuario || !$usuario->estudiante) {
                abort(403, 'No eres estudiante');
            }

            $service = new EstadoDeCuentaService();
            $data = $service->generarEstadoDeCuenta(
                $usuario->estudiante->idEstudiante
            );

            return view(
                'SGFIDMA.moduloEstadoDeCuenta.generarEstadoDeCuenta',
                array_merge($data, [
                    'tipo' => 'mi_estado_cuenta'
                ])
            );

        } catch (\Throwable $e) {

            Log::error('Error al generar estado de cuenta del estudiante', [
                'idUsuario' => auth()->id(),
                'error'     => $e->getMessage(),
            ]);

            return redirect()->back()->with(
                'popupError',
                'No fue posible generar tu estado de cuenta.'
            );
        }
    }


    private function generarNombreArchivo($idEstudiante, $idCiclo)
    {
        $service = new EstadoDeCuentaService();
        $data = $service->generarEstadoDeCuenta($idEstudiante);

        if (!isset($data['estadoCuentaPorCiclo'][$idCiclo])) {
            abort(404, 'Ciclo no encontrado');
        }

        $ciclo = $data['estadoCuentaPorCiclo'][$idCiclo];
        $estudiante = $data['estudiante'];

        $nombreCompleto = trim(
            $estudiante->usuario->primerNombre . ' ' .
            ($estudiante->usuario->segundoNombre ?? '') . ' ' .
            $estudiante->usuario->primerApellido . ' ' .
            ($estudiante->usuario->segundoApellido ?? '')
        );

        // Quitar dobles espacios
        $nombreCompleto = preg_replace('/\s+/', ' ', $nombreCompleto);

        return 'Estado de cuenta_' . $ciclo['nombreCiclo'] . '_' . $nombreCompleto;
    }


    public function exportarEstadoCuentaPDF($idEstudiante, $idCiclo)
    {
        try {

            $service = new EstadoDeCuentaService();
            $data = $service->generarEstadoDeCuenta($idEstudiante);

            if (!isset($data['estadoCuentaPorCiclo'][$idCiclo])) {
                abort(404, 'Ciclo no encontrado');
            }

            $ciclo = $data['estadoCuentaPorCiclo'][$idCiclo];
            $estudiante = $data['estudiante'];

            $pdf = Pdf::loadView(
                'SGFIDMA.moduloEstadoDeCuenta.estadoDeCuentaPDF',
                [
                    'estudiante' => $estudiante,
                    'ciclo'      => $ciclo
                ]
            )->setPaper('letter', 'portrait');

            
            $nombreArchivo = $this->generarNombreArchivo($idEstudiante, $idCiclo);

            return $pdf->download($nombreArchivo . '.pdf');

        } catch (\Throwable $e) {

            Log::error('Error al exportar PDF estado de cuenta', [
                'idEstudiante' => $idEstudiante,
                'idCiclo'      => $idCiclo,
                'error'        => $e->getMessage(),
            ]);

            return back()->with('popupError', 'Error al generar el PDF.');
        }
    }


    public function exportarEstadoCuentaExcel($idEstudiante, $idCiclo)
    {
        try {

            
            $nombreArchivo = $this->generarNombreArchivo($idEstudiante, $idCiclo);

            return Excel::download(
                new EstadoCuentaExport($idEstudiante, $idCiclo),
                $nombreArchivo . '.xlsx'
            );

        } catch (\Throwable $e) {

            Log::error('Error al exportar Excel estado de cuenta', [
                'idEstudiante' => $idEstudiante,
                'idCiclo'      => $idCiclo,
                'error'        => $e->getMessage(),
            ]);

            return back()->with('popupError', 'Error al generar el archivo Excel.');
        }
    }






}
