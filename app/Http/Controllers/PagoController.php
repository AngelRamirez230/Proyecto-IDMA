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
                ->whereDate('fechaDeConclusion', '>=', now())
                ->first();

            // =============================
            // CALCULAR COSTO FINAL
            // =============================
            $costoFinal = $concepto->costo;

            $nombreBeca = null;
            $porcentaje = null;
            $descuento = null;

            if ($esMensualidad && $solicitudBeca) {

                $nombreBeca = $solicitudBeca->nombreDeBeca;
                $porcentaje = $solicitudBeca->porcentajeDeDescuento ?? 0;

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
                'nombreBeca'              => $nombreBeca,
                'porcentajeDeDescuento'   => $porcentaje,
                'descuentoDeBeca'         => $descuento,            
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
            } elseif ($orden === 'mas_reciente') {
                $query->orderBy('fechaGeneracionDePago', 'desc');
            } elseif ($orden === 'mas_antiguo') {
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


    private function generarRecargosAutomaticos($referenciasPagadas = [])
    {
        $fechaHoy = Carbon::today();

        $pagosVencidos = Pago::where('idEstatus', 10)
            ->whereNotNull('fechaLimiteDePago')
            ->whereDate('fechaLimiteDePago', '<', $fechaHoy)
            ->whereNotIn('Referencia', $referenciasPagadas)
            ->lockForUpdate()
            ->get();

        foreach ($pagosVencidos as $pago) {

            if ($pago->idConceptoDePago != 2) continue;

            $mesNumero = Carbon::parse($pago->fechaLimiteDePago)->month;

            $conceptoPorMes = [
                10 => 22, 11 => 23, 12 => 28,
                3  => 29, 1  => 31, 2  => 32,
                4  => 33, 5  => 34, 6  => 35,
                7  => 36, 8  => 37, 9  => 19,
            ];

            if (!isset($conceptoPorMes[$mesNumero])) continue;

            $mesNombre = strtoupper(
                Carbon::create()
                    ->month($mesNumero)
                    ->locale('es')
                    ->translatedFormat('F')
            );

            $yaExiste = Pago::where('referenciaOriginal', $pago->Referencia)
                ->where('idEstatus', 10)
                ->exists();

            if ($yaExiste) continue;

            $concepto = ConceptoDePago::find($conceptoPorMes[$mesNumero]);
            $estudiante = $pago->estudiante;

            if (!$concepto || !$estudiante) continue;

            $pago->update(['idEstatus' => 12]);

            $costoFinal = max(
                $concepto->costo - ($pago->descuentoDeBeca ?? 0),
                0
            );

            $nuevaFechaLimite = Carbon::today()->addDays(8);

            $referenciaNueva = ReferenciaBancariaAztecaService::generar(
                $estudiante,
                $concepto,
                $costoFinal,
                $nuevaFechaLimite
            );

            Pago::create([
                'Referencia' => $referenciaNueva,
                'idEstudiante' => $estudiante->idEstudiante,
                'idConceptoDePago' => $concepto->idConceptoDePago,
                'idCicloModalidad' => $pago->idCicloModalidad,
                'costoConceptoOriginal' => $concepto->costo,
                'nombreBeca' => $pago->nombreBeca,
                'porcentajeDeDescuento' => $pago->porcentajeDeDescuento,
                'descuentoDeBeca' => $pago->descuentoDeBeca,
                'montoAPagar' => $costoFinal,
                'fechaGeneracionDePago' => Carbon::today(),
                'fechaLimiteDePago' => $nuevaFechaLimite,
                'aportacion' => "COLEGIATURA CON RECARGO DEL MES DE {$mesNombre}",
                'idEstatus' => 10,
                'referenciaOriginal' => $pago->Referencia,
            ]);
        }
    }


    public function validarArchivo(Request $request)
    {
        try {

            $request->validate([
                'archivoTxt' => 'required|file|mimes:txt,xlsx,xls|max:20480'
            ], [
                'archivoTxt.max' => 'El archivo supera el tamaño máximo permitido (20MB).',
                'archivoTxt.mimes' => 'El formato del archivo no es válido. Solo se permiten TXT y Excel.',
                'archivoTxt.required' => 'Debe seleccionar un archivo.'
            ]);

            $archivo = $request->file('archivoTxt');
            $extension = strtolower($archivo->getClientOriginalExtension());

            if ($extension === 'txt') {
                return $this->procesarTxt($archivo);
            }

            if (in_array($extension, ['xlsx', 'xls'])) {
                return $this->procesarExcel($archivo);
            }

            return redirect()
                ->back()
                ->with('popupError', 'Formato de archivo no válido.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->with('popupError', $e->validator->errors()->first())
                ->withInput();
        }
    }


    private function procesarTxt($archivo)
    {
        try {

            $pagosActualizados = [];
            $pagosNoEncontrados = [];

            DB::transaction(function () use ($archivo, &$pagosActualizados, &$pagosNoEncontrados) {
                $lineas = file($archivo->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                array_shift($lineas);
                array_pop($lineas);

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

                    $pago = Pago::where('Referencia', $referencia)->lockForUpdate()->first();


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

                $this->generarRecargosAutomaticos($pagosActualizados);

            });

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
        try {

            $pagosActualizados = [];
            $pagosNoEncontrados = [];

            DB::transaction(function () use ($archivo, &$pagosActualizados, &$pagosNoEncontrados) {

                $filas = \Maatwebsite\Excel\Facades\Excel::toArray([], $archivo)[0];

                // Eliminar filas 1,2,3 (titulo + encabezados)
                $filas = array_slice($filas, 3);


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

                    $pago = Pago::whereRaw('TRIM(Referencia) = ?', [$referencia]) ->lockForUpdate()->first();

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

                $this->generarRecargosAutomaticos($pagosActualizados);

            });

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
 



    public function vistaEliminar(Request $request)
    {
        try {

            if (!auth()->user()->esAdmin() && !auth()->user()->esEmpleadoDe(11)) {
                abort(403);
            }

            $query = Pago::with(['estudiante.usuario', 'concepto', 'estatus']);

            // 🔹 SOLO pagos pendientes
            $query->where('idEstatus', 10);

            // 🔹 Buscador opcional
            if ($request->filled('buscarPago')) {
                $buscar = trim($request->buscarPago);

                $query->where('Referencia', 'LIKE', "%{$buscar}%");
            }

            $pagos = $query->paginate(10)->withQueryString();

            return view(
                'SGFIDMA.moduloPagos.eliminarPago',
                compact('pagos')
            );

        } catch (\Throwable $e) {

            \Log::error('Error en vistaEliminar', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea'   => $e->getLine()
            ]);

            return redirect()
                ->route('apartadoPagos')
                ->with(
                    'popupError',
                    'Ocurrió un error al cargar la vista de eliminación de pagos.'
                );
        }
    }


    public function destroy($referencia)
    {
        try {

            $pago = Pago::findOrFail($referencia);

            // 🔹 SOLO permitir eliminar si está pendiente
            if ($pago->idEstatus != 10) {
                return redirect()
                    ->back()
                    ->with('popupError', 'Solo se pueden eliminar pagos pendientes.');
            }

            $pago->delete();

            return redirect()
                ->back()
                ->with('success', 'Pago eliminado correctamente.');

        } catch (\Throwable $e) {

            Log::error('Error al eliminar pago', [
                'referencia' => $referencia,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('popupError', 'No fue posible eliminar el pago.');
        }
    }





}
