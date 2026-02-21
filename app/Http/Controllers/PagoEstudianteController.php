<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// MODELOS
use App\Models\Pago;
use App\Models\Estudiante;
use App\Models\ConceptoDePago;
use App\Models\Notificacion;
use App\Models\CicloModalidad;

use App\Services\ReferenciaBancariaAztecaService;

class PagoEstudianteController extends Controller
{
    

    public function obtenerCiclosPorEstudiantes(Request $request)
    {
        $idsEstudiantes = $request->estudiantes;

        if (!$idsEstudiantes || count($idsEstudiantes) == 0) {
            return response()->json([]);
        }

        // Obtener ciclos donde cada estudiante tiene pagos
        $ciclosPorEstudiante = Pago::whereIn('idEstudiante', $idsEstudiantes)
            ->select('idEstudiante', 'idCicloModalidad')
            ->distinct()
            ->get()
            ->groupBy('idEstudiante');

        $interseccion = null;

        foreach ($ciclosPorEstudiante as $ciclos) {
            $ids = $ciclos->pluck('idCicloModalidad')->toArray();

            if ($interseccion === null) {
                $interseccion = $ids;
            } else {
                $interseccion = array_intersect($interseccion, $ids);
            }
        }

        if (empty($interseccion)) {
            return response()->json([]);
        }

        $ciclos = CicloModalidad::with(['cicloEscolar','modalidad'])
            ->whereIn('idCicloModalidad', $interseccion)
            ->orderByDesc('fechaInicio')
            ->get();

        return response()->json($ciclos);
    }

    
    // =============================
    // FORMULARIO
    // =============================
    public function create(Request $request)
    {
        try {

            $buscar = $request->buscar;
            $filtro = $request->filtro;
            $orden  = $request->orden;

            // =============================
            // QUERY BASE
            // =============================
            $query = Estudiante::with('usuario');

            // =============================
            // BUSCADOR
            // =============================
            if ($request->filled('buscar')) {

                $buscar = trim($buscar);

                $query->whereHas('usuario', function ($u) use ($buscar) {
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

            // =============================
            // FILTRO POR ESTATUS
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

            return view('SGFIDMA.moduloPagos.asignarPagoEstudiante', [
                'estudiantes' => $estudiantes,
                'conceptos'   => ConceptoDePago::where('idEstatus', 1)->get(),
                'ciclos'      => CicloModalidad::with(['cicloEscolar','modalidad'])
                                    ->orderByDesc('fechaInicio')
                                    ->get(),
                'buscar'      => $buscar,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error al cargar formulario de asignación de pagos', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
            ]);

            return redirect()
                ->back()
                ->with(
                    'popupError',
                    'Ocurrió un error al cargar la información. Intente nuevamente o contacte al administrador.'
                );
        }
    }


    public function referenciasVencidas(Request $request)
    {
        $request->validate([
            'idEstudiante'     => 'required|integer',
            'idConceptoDePago' => 'required|integer'
        ]);

        $conceptoId = (int) $request->idConceptoDePago;

        \Log::info('Concepto recibido:', ['concepto' => $conceptoId]);
        \Log::info('Estudiante recibido:', ['estudiante' => $request->idEstudiante]);

        // 🔹 Conceptos con recargo general (1,2,30)
        if (in_array($conceptoId, [1,2,30])) {

            $query = Pago::with('concepto')
                    ->where('idEstudiante', $request->idEstudiante)
                ->whereIn('idConceptoDePago', [1,2,30])
                ->where('idEstatus', 12)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('Pago as p2')
                        ->whereColumn('p2.referenciaOriginal', 'Pago.Referencia')
                        ->whereIn('p2.idEstatus', [10, 11]);
                });

            \Log::info('SQL Recargo:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $referencias = $query->get([
                'Referencia',
                'fechaLimiteDePago',
                'montoAPagar',
                'idCicloModalidad',
                'aportacion',
                'idConceptoDePago'
            ]);

            \Log::info('Referencias encontradas:', ['count' => $referencias->count()]);

            return response()->json(
                $referencias->map(function ($pago) {
                    return [
                        'Referencia'           => $pago->Referencia,
                        'fechaLimiteDePago'    => $pago->fechaLimiteDePago,
                        'montoAPagar'          => $pago->montoAPagar,
                        'idCicloModalidad'     => $pago->idCicloModalidad,
                        'aportacion'           => $pago->aportacion,
                        'nombreConceptoDePago' => $pago->concepto->nombreConceptoDePago ?? null,
                    ];
                })
            );
        }

        // 🔹 Mapeo mensual
        $mesPorConcepto = [
            22 => 10,
            23 => 11,
            28 => 12,
            29 => 3,
            31 => 1,
            32 => 2,
            33 => 4,
            34 => 5,
            35 => 6,
            36 => 7,
            37 => 8,
            19 => 9,
        ];

        if (!isset($mesPorConcepto[$conceptoId])) {
            \Log::info('Concepto no mapeado');
            return response()->json([]);
        }

        $mesEsperado = $mesPorConcepto[$conceptoId];

        \Log::info('Mes esperado:', ['mes' => $mesEsperado]);

        $query = Pago::with('concepto')->where('idEstudiante', $request->idEstudiante)
            ->where('idEstatus', 12)
            ->whereMonth('fechaLimiteDePago', $mesEsperado)
            ->whereDay('fechaLimiteDePago', 15)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('Pago as p2')
                    ->whereColumn('p2.referenciaOriginal', 'Pago.Referencia')
                    ->whereIn('p2.idEstatus', [10, 11]);
            });

        \Log::info('SQL Mensual:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        $referencias = $query->get([
            'Referencia',
            'fechaLimiteDePago',
            'montoAPagar',
            'idCicloModalidad',
            'aportacion',
            'idConceptoDePago'
        ]);

        \Log::info('Referencias encontradas:', ['count' => $referencias->count()]);

        return response()->json(
            $referencias->map(function ($pago) {
                return [
                    'Referencia'           => $pago->Referencia,
                    'fechaLimiteDePago'    => $pago->fechaLimiteDePago,
                    'montoAPagar'          => $pago->montoAPagar,
                    'idCicloModalidad'     => $pago->idCicloModalidad,
                    'aportacion'           => $pago->aportacion,
                    'nombreConceptoDePago' => $pago->concepto->nombreConceptoDePago ?? null,
                ];
            })
        );
    }




    // =============================
    // GUARDAR PAGOS
    // =============================
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'idConceptoDePago'   => 'required|exists:concepto_de_pago,idConceptoDePago',
                'fechaEmisionDePago' => 'required|date|after_or_equal:today',
                'fechaLimiteDePago'  => 'required|date|after_or_equal:fechaEmisionDePago',
                'estudiantes'        => 'required|array|min:1',
                'estudiantes.*'      => 'exists:estudiante,idEstudiante',
                'aportacion'         => 'required|string|max:100',
                'idCicloModalidad' => 'nullable|exists:ciclo_modalidad,idCicloModalidad',
                'descuentoDePago' => 'nullable|numeric|min:0',
                'referenciaOriginal' => [
                                            'nullable',
                                            'exists:Pago,Referencia',
                                            function ($attribute, $value, $fail) use ($request) {

                                                $existePendiente = Pago::where('referenciaOriginal', $value)
                                                    ->where('idEstatus', 10)
                                                    ->exists();

                                                if ($existePendiente) {
                                                    $fail('Ya existe un recargo pendiente para esa referencia.');
                                                }
                                            }
                                        ],
            ],
            [
                'required' => 'El campo :attribute es obligatorio.',
                'string'   => 'El campo :attribute debe ser texto.',
                'date'     => 'El campo :attribute debe ser una fecha válida.',
                'array'    => 'Debe seleccionar al menos un estudiante.',
                'min'      => 'Debe seleccionar al menos :min estudiante.',
                'max'      => 'El campo :attribute no debe exceder :max caracteres.',
                'exists'   => 'El :attribute seleccionado no es válido.',
                'after_or_equal' => 'La :attribute no puede ser menor a la fecha de emisión.',
            ],
            [
                'idConceptoDePago'   => 'concepto de pago',
                'fechaEmisionDePago'=> 'fecha de emisión de pago',
                'fechaLimiteDePago' => 'fecha límite de pago',
                'estudiantes'       => 'estudiantes',
                'aportacion'        => 'aportación',
            ]
        );


        if ($validator->fails()) {
            return back()
                ->with('popupError', 'No se pudieron generar los pagos. Verifica la información.')
                ->withErrors($validator)
                ->withInput();
        }

        // VALIDACIÓN PERSONALIZADA
        if (!$request->referenciaOriginal && !$request->idCicloModalidad) {
            return back()->withErrors([
                'idCicloModalidad' => 'Debe seleccionar un ciclo escolar.'
            ])->withInput();
        }

        $concepto = ConceptoDePago::findOrFail($request->idConceptoDePago);
        $fechaLimitePago = Carbon::parse($request->fechaLimiteDePago);
        $fechaEmisionPago = Carbon::parse($request->fechaEmisionDePago);



        $contadorReferencias = 0;
        $referenciasCreadas = [];
        $referenciasDuplicadas = [];
        $omitidosPorPlan = [];


        try {


            // =============================
            // TRANSACCIÓN
            // =============================
            DB::transaction(function () use ($request,$concepto,$fechaLimitePago,$fechaEmisionPago,&$contadorReferencias,&$referenciasCreadas,&$referenciasDuplicadas,&$omitidosPorPlan) 
            {

                $idCicloFinal = $request->idCicloModalidad;

                if ($request->referenciaOriginal) {
                    $pagoOriginal = Pago::where('Referencia', $request->referenciaOriginal)->first();

                    if ($pagoOriginal) {
                        $idCicloFinal = $pagoOriginal->idCicloModalidad;
                    }
                }


                foreach ($request->estudiantes as $idEstudiante) {

                    $estudiante = Estudiante::with('usuario')->findOrFail($idEstudiante);



                    // =============================
                    // VALIDAR CICLO ESCOLAR
                    // =============================
                    $cicloActual = $estudiante->idCicloModalidad;

                    $tuvoCiclo = Pago::where('idEstudiante', $idEstudiante)
                        ->where('idCicloModalidad', $idCicloFinal)
                        ->exists();

                    if ($cicloActual != $idCicloFinal && !$tuvoCiclo) {

                        $omitidosPorPlan[] = [
                            'estudiante' => $estudiante->usuario->primerNombre . ' ' .
                                            $estudiante->usuario->segundoNombre . ' ' .
                                            $estudiante->usuario->primerApellido . ' ' .
                                            $estudiante->usuario->segundoApellido,
                            'concepto'   => $concepto->nombreConceptoDePago,
                            'motivo'     => 'Nunca ha tenido asignado el ciclo escolar seleccionado',
                        ];

                        continue;
                    }


                    // =============================
                    // VALIDAR PLAN DE PAGO ACTIVO
                    // =============================
                    $conceptosRestringidos = [1, 2, 30];

                    if (
                        $estudiante->tienePlanActivo() &&
                        in_array($concepto->idConceptoDePago, $conceptosRestringidos)
                    ) {

                        $omitidosPorPlan[] = [
                            'estudiante' => $estudiante->usuario->primerNombre . ' ' .
                                            $estudiante->usuario->segundoNombre . ' ' .
                                            $estudiante->usuario->primerApellido . ' ' .
                                            $estudiante->usuario->segundoApellido,
                            'concepto'   => $concepto->nombreConceptoDePago,
                            'motivo'     => 'Cuenta con plan de pago activo',
                        ];

                        continue;
                    }


                    // =============================
                    // CALCULAR MONTO FINAL
                    // =============================
                    $conceptosHeredanBeca = [19,22,23,28,29,31,32,33,34,35,36,37];
                    $costoOriginal = $concepto->costo;
                    $costoFinal    = $costoOriginal;

                    $porcentajeBeca = 0;
                    $descuentoBeca  = 0;
                    $nombreBeca     = null;

                    // =============================
                    // HEREDAR BECA DESDE REFERENCIA ORIGINAL
                    // =============================
                    if (
                        $request->referenciaOriginal &&
                        in_array($concepto->idConceptoDePago, $conceptosHeredanBeca)
                    ) {

                        $pagoOriginal = Pago::where('Referencia', $request->referenciaOriginal)->first();

                        if ($pagoOriginal) {

                            $nombreBeca          = $pagoOriginal->nombreBeca ?? null;
                            $porcentajeBeca      = $pagoOriginal->porcentajeDeDescuento ?? null;
                            $descuentoBeca       = $pagoOriginal->descuentoDeBeca ?? null;

                            // Restar descuento heredado
                            $costoFinal -= $descuentoBeca;
                            $costoFinal = max($costoFinal, 0);
                        }
                    }

                    // ¿Es mensualidad?
                    $esMensualidad = ($concepto->idConceptoDePago == 2);

                    // =============================
                    // VALIDAR BECA
                    // =============================
                    $ignorarBeca = (
                        $fechaLimitePago->day == 15 &&
                        in_array($fechaLimitePago->month, [3, 9])
                    );

                    if ($esMensualidad && !$ignorarBeca && !$request->referenciaOriginal ) {

                        $solicitudBeca = $estudiante->solicitudesDeBeca()
                            ->where('idEstatus', 6)
                            ->with('beca')
                            ->first();

                        if ($solicitudBeca && $solicitudBeca->beca) {

                            $porcentajeBeca = $solicitudBeca->beca->porcentajeDeDescuento;
                            $descuentoBeca  = ($costoOriginal * $porcentajeBeca) / 100;
                            $nombreBeca     = optional($solicitudBeca->beca)->nombreDeBeca;

                            $costoFinal -= $descuentoBeca;
                        }
                    }

                    // =============================
                    // DESCUENTO MANUAL
                    // =============================
                    $descuentoManual = $request->descuentoDePago ?? 0;

                    // Evitar que el descuento sea mayor al monto actual
                    if ($descuentoManual > $costoFinal) {
                        $descuentoManual = $costoFinal;
                    }

                    $costoFinal -= $descuentoManual;

                    // Evitar negativos
                    $costoFinal = max($costoFinal, 0);



                    // =============================
                    // GENERAR REFERENCIA
                    // =============================
                    $referenciaFinal = ReferenciaBancariaAztecaService::generar(
                        $estudiante,
                        $concepto,
                        $costoFinal,
                        $fechaLimitePago
                    );


                    // =============================
                    // VERIFICAR DUPLICADO
                    // =============================
                    $pagoExistente = Pago::where('Referencia', $referenciaFinal)
                                        ->where('idEstudiante', $estudiante->idEstudiante)
                                        ->first();

                    if (!$pagoExistente) {
                        // =============================
                        // GUARDAR PAGO
                        // =============================
                        Pago::create([
                            'Referencia'               => $referenciaFinal,
                            'idEstudiante'             => $estudiante->idEstudiante,
                            'idConceptoDePago'         => $concepto->idConceptoDePago,
                            'idCicloModalidad'         => $idCicloFinal,

                            'costoConceptoOriginal'    => $costoOriginal,
                            'nombreBeca'               => $nombreBeca,
                            'porcentajeDeDescuento'    => $porcentajeBeca,
                            'descuentoDeBeca'          => $descuentoBeca,
                            'descuentoDePago'          => $descuentoManual,

                            'montoAPagar'              => $costoFinal,

                            'fechaGeneracionDePago'    => $fechaEmisionPago,
                            'fechaLimiteDePago'        => $fechaLimitePago,
                            'aportacion'               => $request->aportacion,
                            'idEstatus'                => 10,
                            'referenciaOriginal'       => $request->referenciaOriginal,
                        ]);



                        $referenciasCreadas[] = [
                            'estudiante' => $estudiante->usuario->primerNombre . ' ' .
                                            $estudiante->usuario->segundoNombre . ' ' .
                                            $estudiante->usuario->primerApellido . ' ' .
                                            $estudiante->usuario->segundoApellido,
                            'referencia' => $referenciaFinal,
                            'concepto'   => $concepto->nombreConceptoDePago,
                            'fecha'      => $fechaLimitePago->format('Y-m-d'),
                        ];

                        $contadorReferencias++;

                        // =============================
                        // CREAR NOTIFICACIÓN PARA EL ESTUDIANTE
                        // =============================
                        Notificacion::create([
                            'idUsuario'         => $estudiante->idUsuario,
                            'titulo'            => 'Nuevo pago asignado',
                            'mensaje'           => "Se te ha asignado el concepto de pago: {$concepto->nombreConceptoDePago}. Revisa tus pagos.",
                            'tipoDeNotificacion'=> 1, // Informativo
                            'fechaDeInicio' => $fechaEmisionPago->toDateString(),
                            'fechaFin'           => $fechaEmisionPago->copy()->addDays(3)->toDateString(),
                            'leida'             => 0,
                        ]);
                    } else {
                        $referenciasDuplicadas[] = [
                            'estudiante' => $estudiante->usuario->primerNombre . ' ' .
                                            $estudiante->usuario->segundoNombre . ' ' .
                                            $estudiante->usuario->primerApellido . ' ' .
                                            $estudiante->usuario->segundoApellido,
                            'referencia' => $referenciaFinal,
                            'concepto'   => $concepto->nombreConceptoDePago,
                            'fecha'      => $fechaLimitePago->format('Y-m-d'),
                        ];
                    }
                }
            });
        } catch (\Throwable $e) {
            return back()->with('popupError', 'Ocurrió un error al generar los pagos.');
        }

        // =============================
        // MENSAJE FINAL
        // =============================
        $mensajePopup = "{$contadorReferencias} referencias generadas. " .
                        count($referenciasDuplicadas) . " referencias ya existían.";

        return redirect()
            ->route('pagos.detalles-referencias')
            ->with('successPagos', true)
            ->with('creados', $referenciasCreadas)
            ->with('duplicados', $referenciasDuplicadas)
            ->with('omitidos', $omitidosPorPlan);;
        
    }


   public function detallesReferencias()
    {
        try {

            return view('SGFIDMA.moduloPagos.detallesGeneracionDeReferencias', [
                'creados'    => session('creados', []),
                'duplicados' => session('duplicados', []),
                'omitidos'   => session('omitidos', []),
            ]);

        } catch (\Throwable $e) {
            Log::error('Error al cargar detalles de generación de referencias', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
            ]);

            return redirect()
                ->route('admin.pagos.create') 
                ->with(
                    'popupError',
                    'Ocurrió un error al mostrar los detalles de las referencias.'
                );
        }
    }
}
