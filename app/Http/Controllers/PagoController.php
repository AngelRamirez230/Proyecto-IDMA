<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


// MODELOS
use App\Models\Pago;
use App\Models\ConceptoDePago;
use App\Services\ReferenciaBancariaAztecaService;

class PagoController extends Controller
{
    public function generarReferencia($idConcepto)
    {
        DB::beginTransaction();

        try {
            // =============================
            // USUARIO Y ESTUDIANTE
            // =============================
            $usuario = Auth::user();
            $estudiante = $usuario->estudiante;

            if (!$estudiante) {
                return redirect()
                    ->back()
                    ->with('popupError', 'Estudiante no encontrado.');
            }





            // =============================
            // CONCEPTO DE PAGO
            // =============================
            $concepto = ConceptoDePago::findOrFail($idConcepto);

            // =============================
            // VALIDAR SI ES COLEGIATURA
            // =============================
            $esMensualidad = ($concepto->idConceptoDePago == 2);

            // =============================
            // BUSCAR BECA APROBADA
            // =============================
            $solicitudBeca = $estudiante->solicitudesDeBeca()
                ->where('idEstatus', 6)
                ->with('beca')
                ->first();

            // =============================
            // CALCULAR COSTO FINAL
            // =============================
            $costoFinal = $concepto->costo;

            if ($esMensualidad && $solicitudBeca && $solicitudBeca->beca) {
                $porcentaje = $solicitudBeca->beca->porcentajeDeDescuento;
                $descuento = ($concepto->costo * $porcentaje) / 100;
                $costoFinal = $concepto->costo - $descuento;
            }

            // =============================
            // NOMBRE COMPLETO
            // =============================
            $nombreCompleto = trim(
                $usuario->primerNombre . ' ' .
                $usuario->segundoNombre . ' ' .
                $usuario->primerApellido . ' ' .
                $usuario->segundoApellido
            );

            // =============================
            // FECHA LÍMITE DE PAGO
            // =============================
            $fechaGeneracion = Carbon::today();
            $fechaLimitePago = $fechaGeneracion->copy()->addDays(8);

            if ($fechaLimitePago->month !== $fechaGeneracion->month) {
                $fechaLimitePago = $fechaGeneracion->copy()->endOfMonth();
            }


            // =============================
            // VALIDAR PAGO PENDIENTE EXISTENTE
            // =============================
            $pagoPendiente = Pago::where('idEstudiante', $estudiante->idEstudiante)
                ->where('idConceptoDePago', $concepto->idConceptoDePago)
                ->where('idEstatus', 10) // Pendiente
                ->first();

            if ($pagoPendiente) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with('popupError', 'Ya cuentas con un pago pendiente para este concepto.');
            }



            // =============================
            // GENERAR REFERENCIA (SERVICE)
            // =============================
            $referenciaFinal = ReferenciaBancariaAztecaService::generar(
                $estudiante,
                $concepto,
                $costoFinal,
                $fechaLimitePago
            );


            // =============================
            // VALIDAR DUPLICADO
            // =============================
            if (Pago::where('Referencia', $referenciaFinal)->exists()) {
                return redirect()
                    ->back()
                    ->with('popupError', 'La referencia bancaria ya existe.');
            }


            if ($estudiante->cicloModalidad->idTipoDeEstatus != 1) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with('popupError', 'El estudiante tiene un ciclo escolar que no está activo.');
            }



            // =============================
            // GUARDAR PAGO
            // =============================
            Pago::create([
                'Referencia'              => $referenciaFinal,
                'idCicloModalidad'        => $estudiante->idCicloModalidad,
                'idConceptoDePago'        => $concepto->idConceptoDePago,
                'costoConceptoOriginal'   => $concepto->costo,              
                'montoAPagar'             => $costoFinal,                   
                'fechaGeneracionDePago'   => now(),
                'fechaLimiteDePago'       => $fechaLimitePago,
                'aportacion'              => null,
                'idEstatus'               => 10,
                'idEstudiante'            => $estudiante->idEstudiante,
            ]);


            DB::commit();

            // =============================
            // PDF
            // =============================
            $pdf = Pdf::loadView(
                'SGFIDMA.moduloPagos.formatoReferenciaDePago',
                [
                    'referencia'     => $referenciaFinal,
                    'estudiante'     => $estudiante,
                    'concepto'       => $concepto,
                    'nombreCompleto' => $nombreCompleto,
                    'fechaEmision'   => now()->format('d/m/Y'),
                    'fechaLimite'    => $fechaLimitePago->format('d/m/Y'),
                    'montoAPagar'    => $costoFinal,
                ]
            )->setPaper('letter');

            return $pdf->download('Referencia_de_Pago.pdf');

        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error('Error al generar referencia de pago', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->with('popupError', 'No se pudo generar la referencia de pago. Intente mas tarde');
        }
    }



    public function descargarRecibo($referencia)
    {
        try {

            $pago = Pago::with([
                'estudiante.usuario',
                'concepto'
            ])->findOrFail($referencia);

            $usuario = $pago->estudiante->usuario;

            $nombreCompleto = trim(
                $usuario->primerNombre . ' ' .
                $usuario->segundoNombre . ' ' .
                $usuario->primerApellido . ' ' .
                $usuario->segundoApellido
            );

            $pdf = Pdf::loadView(
                'SGFIDMA.moduloPagos.formatoReferenciaDePago',
                [
                    'referencia'     => $pago->Referencia,
                    'estudiante'     => $pago->estudiante,
                    'concepto'       => $pago->concepto,
                    'nombreCompleto' => $nombreCompleto,
                    'fechaEmision'   => $pago->fechaGeneracionDePago?->format('d/m/Y'),
                    'fechaLimite'    => $pago->fechaLimiteDePago?->format('d/m/Y'),
                    'montoAPagar'    => $pago->montoAPagar,
                    'pago'           => $pago,
                ]
            )->setPaper('letter');

            return $pdf->download('Recibo_Pago_' . $pago->Referencia . '.pdf');

        } catch (\Exception $e) {

            \Log::error('Error al generar recibo PDF', [
                'referencia' => $referencia,
                'error'      => $e->getMessage()
            ]);

            return redirect()->back()->with('popupError', 'No fue posible generar el recibo de pago. Intente mas tarde');
        }
    }



    // =============================
    // CONSULTA DE PAGOS
    // =============================
    public function index(Request $request)
    {
        try {

            $orden  = $request->orden;
            $filtro = $request->filtro;
            $buscar = $request->buscarPago;

            $usuario = Auth::user();

            $query = Pago::with([
                'estudiante.usuario',
                'concepto',
                'estatus'
            ]);

            // =============================
            // RESTRICCIÓN POR ROL (ESTUDIANTE)
            // =============================
            if ($usuario->estudiante) {
                $query->where('idEstudiante', $usuario->estudiante->idEstudiante)
                    ->whereDate('fechaGeneracionDePago', '<=', Carbon::today());
            }

            // =============================
            // BUSCADOR
            // =============================
            if ($request->filled('buscarPago')) {

                $buscar = trim($buscar);

                $query->where(function ($q) use ($buscar, $usuario) {

                    
                    $q->where('Referencia', 'LIKE', "%{$buscar}%");

    
                    if (!$usuario->estudiante) {

                        $q->orWhereHas('estudiante.usuario', function ($u) use ($buscar) {

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
                        });
                    }
                });
            }


            // =============================
            // FILTRO POR ESTATUS
            // =============================
            if ($filtro === 'pendientes') {
                $query->where('idEstatus', 10);
            } elseif ($filtro === 'aprobados') {
                $query->where('idEstatus', 11);
            } elseif ($filtro === 'rechazados') {
                $query->where('idEstatus', 12);
            }

            // =============================
            // ORDENAMIENTO
            // =============================
            if ($orden === 'alfabetico') {
                $query->orderBy('idEstudiante');
            } elseif ($orden === 'porcentaje_mayor') {
                $query->orderBy('fechaGeneracionDePago', 'desc');
            } elseif ($orden === 'porcentaje_menor') {
                $query->orderBy('fechaGeneracionDePago', 'asc');
            } else {
                // Orden por defecto
                $query->orderBy('fechaGeneracionDePago', 'desc');
            }

            $pagos = $query->paginate(10)->withQueryString();

            return view(
                'SGFIDMA.moduloPagos.consultaDePagos',
                compact('pagos', 'orden', 'filtro', 'buscar')
            );

        } catch (\Throwable $e) {

            \Log::error('Error en consulta de pagos', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea'   => $e->getLine(),
            ]);

            return redirect()->back()->with(
                'popupError',
                'Ocurrió un error al consultar los pagos.'
            );
        }
    }



    public function show($referencia)
    {
        try {

            $pago = Pago::with([
                'estudiante.usuario',
                'concepto',
                'estatus'
            ])->findOrFail($referencia);

            return view('SGFIDMA.moduloPagos.detallesDePago', [
                'pago' => $pago
            ]);

        } catch (\Throwable $e) {

            Log::error('Error al cargar detalles del pago', [
                'referencia' => $referencia,
                'error'      => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('popupError', 'Ocurrió un error al cargar los detalles del pago');
        }
    }




   // Mostrar la vista de validación de pagos pendientes
    public function vistaValidarPagos()
    {
        try {

            $pagos = Pago::with([
                    'estudiante.usuario',
                    'concepto',
                    'estatus'
                ])
                ->where('idEstatus', 10)
                ->paginate(10);

            return view(
                'SGFIDMA.moduloPagos.validacionDePagos',
                compact('pagos')
            );

        } catch (\Throwable $e) {

            Log::error('Error al cargar vista de validación de pagos', [
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with(
                    'popupError',
                    'No se pudo cargar ingresar a este apartado. Intente mas tarde'
                );
        }
    }



    public function validarArchivo(Request $request)
    {
        $request->validate([
            'archivoTxt' => 'required|file|mimes:txt,xlsx,xls'
        ]);

        $archivo = $request->file('archivoTxt');
        $extension = strtolower($archivo->getClientOriginalExtension());

        if ($extension === 'txt') {
            return $this->procesarTxt($archivo);
        }

        if (in_array($extension, ['xlsx', 'xls'])) {
            return $this->procesarExcel($archivo);
        }

        return redirect()->back()->with('popupError', 'Formato de archivo no válido.');
    }


    private function procesarTxt($archivo)
    {
        try{

            $lineas = file($archivo->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            array_shift($lineas);
            array_pop($lineas);

            $pagosActualizados = [];
            $pagosNoEncontrados = [];

            foreach ($lineas as $linea) {

                $tipoRegistro  = substr($linea, 0, 1);
                $fechaPagoTxt = substr($linea, 1, 8);
                $referencia   = trim(substr($linea, 9, 31));
                $operacionBaz = substr($linea, 40, 10);
                $sucursal     = substr($linea, 51, 4);
                $formaPago    = substr($linea, 54, 2);
                $importePago  = substr($linea, 56, 11);
                $comision     = substr($linea, 67, 11);
                $iva          = substr($linea, 78, 11);
                $importeNeto  = substr($linea, 89, 11);

                $fechaPago = Carbon::createFromFormat('Ymd', $fechaPagoTxt);

                $importePago = number_format(((int)$importePago) / 100, 2, '.', '');
                $comision    = number_format(((int)$comision) / 100, 2, '.', '');
                $iva         = number_format(((int)$iva) / 100, 2, '.', '');
                $importeNeto = number_format(((int)$importeNeto) / 100, 2, '.', '');

                $pago = Pago::where('Referencia', $referencia)->first();


                if (!$pago) {
                    $pagosNoEncontrados[] = $referencia;
                    continue;
                }

                if ($pago->idEstatus == 11) continue;

                $pago->update([
                    'fechaDePago' => $fechaPago,
                    'numeroDeOperaciónBAZ' => $operacionBaz,
                    'numeroDeSucursal' => $sucursal,
                    'idTipoDePago' => $formaPago,
                    'ImporteDePago' => $importePago,
                    'comisión' => $comision,
                    'IVA' => $iva,
                    'ImporteNeto' => $importeNeto,
                    'tipoDeRegistro' => $tipoRegistro,
                    'idEstatus' => 11,
                ]);

                $pagosActualizados[] = $referencia;
            }

            $nombreArchivo = $archivo->getClientOriginalName();

            return redirect()->back()->with('success',
                "TXT cargado correctamente<br>
                <strong>Archivo cargado</strong>: {$nombreArchivo}<br>
                <strong>Pagos validados:</strong> " . count($pagosActualizados) .
                "<br><strong>No encontrados:</strong> " . count($pagosNoEncontrados)
            );

        } catch (\Throwable $e) {

            Log::error('Error general al procesar TXT', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with(
                    'popupError',
                    'Ocurrió un error al procesar el archivo TXT'
                );
        }
    }



    private function procesarExcel($archivo)
    {
        try{

            $filas = \Maatwebsite\Excel\Facades\Excel::toArray([], $archivo)[0];

            // Eliminar filas 1,2,3 (titulo + encabezados)
            $filas = array_slice($filas, 3);

            $pagosActualizados = [];
            $pagosNoEncontrados = [];

            foreach ($filas as $fila) {

                if (count($fila) < 13) continue;

                // ================================
                // LIMPIEZA DE DATOS
                // ================================

                $fechaHora  = trim((string) $fila[0]);
                $noSucursal = trim((string) $fila[3]);
                $operacionBaz = trim((string) $fila[5]);

                $tipoOperacion = trim((string) $fila[6]);
                $referencia = trim((string) $fila[8]);

                // Limpieza extra para caracteres invisibles
                $referencia = preg_replace('/\s+/', '', $referencia);

                $importePago = floatval($fila[9]);
                $comision    = floatval($fila[10]);
                $iva         = floatval($fila[11]);
                $importeNeto = floatval($fila[12]);

                if (!$referencia) continue;

                // ================================
                // LIMPIEZA DE FECHA
                // ================================
                try {
                    $fechaPago = Carbon::createFromFormat('d/m/Y H:i:s', $fechaHora)->format('Y-m-d');
                } catch (\Exception $e) {
                    continue;
                }

                // ================================
                // NORMALIZAR FORMA DE PAGO
                // ================================

                $tipoOperacionNormalizado = mb_strtolower($tipoOperacion);
                $tipoOperacionNormalizado = str_replace(
                    ['á','é','í','ó','ú','ñ'],
                    ['a','e','i','o','u','n'],
                    $tipoOperacionNormalizado
                );

                if (str_contains($tipoOperacionNormalizado, 'efectivo')) {
                    $formaPago = 1;
                } elseif (
                    str_contains($tipoOperacionNormalizado, 'deposit') ||
                    str_contains($tipoOperacionNormalizado, 'dep')
                ) {
                    $formaPago = 3;
                } else {
                    $formaPago = null;
                }

                if (!$formaPago) {
                    continue;
                }
                

                // ================================
                // BUSCAR PAGO
                // ================================

                $pago = Pago::whereRaw('TRIM(Referencia) = ?', [$referencia])->first();

                if (!$pago) {
                    $pagosNoEncontrados[] = $referencia;
                    continue;
                }

                if ($pago->idEstatus == 11) continue;

                // ================================
                // ACTUALIZAR
                // ================================

                $pago->update([
                    'fechaDePago' => $fechaPago,
                    'numeroDeSucursal' => $noSucursal,
                    'numeroDeOperaciónBAZ' => $operacionBaz,
                    'idTipoDePago' => $formaPago,
                    'ImporteDePago' => $importePago,
                    'comisión' => $comision,
                    'IVA' => $iva,
                    'ImporteNeto' => $importeNeto,
                    'tipoDeRegistro' => 'D',
                    'idEstatus' => 11,
                ]);

                $pagosActualizados[] = $referencia;
            }

            $nombreArchivo = $archivo->getClientOriginalName();

            return redirect()->back()->with('success',
                "Excel cargado correctamente<br>
                <strong>Archivo cargado</strong>: {$nombreArchivo}<br>
                <strong>Pagos validados:</strong> " . count($pagosActualizados) .
                "<br><strong>No encontrados:</strong> " . count($pagosNoEncontrados)
            );
        } catch (\Throwable $e) {

            Log::error('Error general al procesar Excel', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with(
                    'popupError',
                    'Ocurrió un error al procesar el archivo Excel'
                );
        }

    }

}
