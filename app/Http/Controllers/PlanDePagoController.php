<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\Models\PlanDePago;
use App\Models\PlanConcepto;
use App\Models\ConceptoDePago;
use App\Models\Estudiante;
use App\Models\EstudiantePlan;
use App\Models\Notificacion;
use App\Models\Pago;


class PlanDePagoController extends Controller
{
    public function create()
    {
        try {

            $conceptos = ConceptoDePago::whereIn('idConceptoDePago', [1, 2, 30])
                ->where('idEstatus', 1)
                ->get();

            return view('SGFIDMA.moduloPlanDePago.altaPlan', compact('conceptos'));

        } catch (\Throwable $e) {

            \Log::error('Error al cargar vista de alta de plan de pago', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with(
                    'popupError',
                    'Ocurrió un error al cargar la vista de alta de plan de pago.'
                );
        }
    }


    public function store(Request $request)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    // ======================
                    // PLAN DE PAGO
                    // ======================
                    'nombrePlan' => 'required|string|max:150',
                    'cantidades' => 'required|array',
                ],
                [
                    // ======================
                    // MENSAJES GENERALES
                    // ======================
                    'required' => 'El campo :attribute es obligatorio.',
                    'string'   => 'El campo :attribute debe ser texto.',
                    'max'      => 'El campo :attribute no debe exceder :max caracteres.',
                    'array'    => 'El campo :attribute no tiene un formato válido.',
                ],
                [
                    // ======================
                    // NOMBRES AMIGABLES
                    // ======================
                    'nombrePlan' => 'nombre del plan de pago',
                    'cantidades' => 'conceptos del plan de pago',
                ]
            );

            // ⛔ Validación
            if ($validator->fails()) {
                return back()
                    ->with('popupError', 'No se pudo crear el plan de pago. Verifica los datos ingresados.')
                    ->withErrors($validator)
                    ->withInput();
            }

            // ======================
            // VALIDAR DUPLICADOS
            // ======================
            $existe = PlanDePago::whereRaw(
                'LOWER(nombrePlanDePago) = ?',
                [mb_strtolower(trim($request->nombrePlan))]
            )->exists();

            if ($existe) {
                return back()
                    ->with('popupError', 'Ya existe un plan de pago con ese nombre.')
                    ->withInput();
            }

            // Formatear nombre
            $nombre = $this->mbUcwords($request->nombrePlan);

            // ======================
            // VALIDAR QUE HAYA CONCEPTOS
            // ======================
            $todasCero = collect($request->cantidades)->every(function ($cantidad) {
                return intval($cantidad) <= 0;
            });

            if ($todasCero) {
                return back()
                    ->with('popupError', 'El plan de pago debe tener conceptos seleccionados.')
                    ->withInput();
            }

            // ======================
            // REGLAS DE NEGOCIO
            // ======================
            $cantidades = collect($request->cantidades);

            $inscripcion   = intval($cantidades->get(1, 0));
            $reinscripcion = intval($cantidades->get(30, 0));
            $colegiaturas  = intval($cantidades->get(2, 0));

            if (
                ($inscripcion > 0 && $reinscripcion > 0) ||
                ($inscripcion === 0 && $reinscripcion === 0)
            ) {
                return back()
                    ->withErrors([
                        'cantidades' => 'El plan debe incluir INSCRIPCIÓN o REINSCRIPCIÓN (solo uno de ellos).'
                    ])
                    ->withInput();
            }

            if ($colegiaturas !== 6) {
                return back()
                    ->withErrors([
                        'cantidades' => 'El plan debe incluir exactamente 6 colegiaturas.'
                    ])
                    ->withInput();
            }

            // ======================
            // CREAR PLAN
            // ======================
            $plan = PlanDePago::create([
                'nombrePlanDePago' => $nombre,
                'idEstatus' => 1
            ]);

            // ======================
            // GUARDAR CONCEPTOS
            // ======================
            foreach ($request->cantidades as $idConcepto => $cantidad) {
                $cantidad = intval($cantidad);

                if ($cantidad > 0) {
                    PlanConcepto::create([
                        'idPlanDePago'     => $plan->idPlanDePago,
                        'idConceptoDePago' => $idConcepto,
                        'cantidad'         => $cantidad
                    ]);
                }
            }

            return redirect()
                ->route('altaPlan')
                ->with('success', 'Plan de pagos creado correctamente.');

        } catch (\Throwable $e) {

            \Log::error('Error al crear plan de pago', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('popupError', 'Ocurrió un error al crear el plan de pago. Intenta más tarde.');
        }
    }


    private function mbUcwords($string, $encoding = 'UTF-8')
    {
        $string = mb_strtolower($string, $encoding);
        $words = explode(' ', $string);

        foreach ($words as &$word) {
            if ($word !== '') {
                $first = mb_substr($word, 0, 1, $encoding);
                $rest = mb_substr($word, 1, null, $encoding);
                $word = mb_strtoupper($first, $encoding) . $rest;
            }
        }

        return implode(' ', $words);
    }


    public function index(Request $request)
    {
        try {

            $buscar = $request->buscarPlan;
            $filtro = $request->filtro;
            $orden  = $request->orden;

            $plan = PlanDePago::with('estatus');

            // ======================
            // BUSCAR
            // ======================
            if (!empty($buscar)) {
                $plan->where('nombrePlanDePago', 'LIKE', '%' . $buscar . '%');
            }

            // ======================
            // FILTRO
            // ======================
            if ($filtro == 'activas') {
                $plan->where('idEstatus', 1);
            } elseif ($filtro == 'suspendidas') {
                $plan->where('idEstatus', 2);
            }

            // ======================
            // ORDEN
            // ======================
            if ($orden == 'alfabetico') {
                $plan->orderBy('nombrePlanDePago', 'ASC');
            }

            // ======================
            // PAGINACIÓN
            // ======================
            $planes = $plan->paginate(10)->withQueryString();

            return view(
                'SGFIDMA.moduloPlanDePago.consultaPlanDePago',
                compact('planes', 'buscar', 'filtro', 'orden')
            );

        } catch (\Throwable $e) {

            \Log::error('Error al cargar consulta de planes de pago', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with(
                    'popupError',
                    'Ocurrió un error al cargar los planes de pago.'
                );
        }
    }



    public function edit($id)
    {
        try {

            $plan = PlanDePago::with('conceptos')->findOrFail($id);

            // Obtener todos los conceptos activos
            $conceptos = ConceptoDePago::whereIn('idConceptoDePago', [1, 2, 30])
                ->where('idEstatus', 1)
                ->get();

            // Convertir cantidades actuales en un arreglo [idConcepto => cantidad]
            $cantidadesActuales = $plan->conceptos
                ->pluck('cantidad', 'idConceptoDePago')
                ->toArray();

            return view(
                'SGFIDMA.moduloPlanDePago.modificacionPlan',
                compact('plan', 'conceptos', 'cantidadesActuales')
            );

        } catch (\Throwable $e) {

            \Log::error('Error al cargar edición de plan de pago', [
                'id_plan' => $id,
                'error'   => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with(
                    'popupError',
                    'No se pudo cargar la información del plan de pago.'
                );
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $plan = PlanDePago::findOrFail($id);

            /*
            ==================================================
            CAMBIO DE ESTATUS
            ==================================================
            */
            if ($request->accion === 'Suspender/Habilitar') {

                $tieneEstudiantesActivos = $plan->estudiantes()
                    ->where('idEstatus', 1) // 1 = activo
                    ->exists();

                if ($plan->idEstatus == 1 && $tieneEstudiantesActivos) {
                    return redirect()
                        ->route('consultaPlan')
                        ->with('popupError', 'No se puede suspender este plan porque existen estudiantes con este plan asignado.');
                }

                $estatusAnterior = $plan->idEstatus;
                $plan->idEstatus = ($plan->idEstatus == 1) ? 2 : 1;
                $plan->save();

                $mensaje = ($estatusAnterior == 1)
                    ? "El plan de pago {$plan->nombrePlanDePago} ha sido suspendido."
                    : "El plan de pago {$plan->nombrePlanDePago} ha sido activado.";

                return redirect()->route('consultaPlan')->with('success', $mensaje);
            }

            /*
            ==================================================
            VALIDACIÓN DEL NOMBRE
            ==================================================
            */
            $request->validate([
                'nombrePlan' => 'required|string|max:150',
            ]);

            $nombreFormateado = $this->mbUcwords($request->nombrePlan);

            $existe = PlanDePago::whereRaw(
                    'LOWER(nombrePlanDePago) = ?',
                    [mb_strtolower($nombreFormateado)]
                )
                ->where('idPlanDePago', '!=', $id)
                ->exists();

            if ($existe) {
                return back()
                    ->with('popupError', 'Ya existe un plan de pago con ese nombre.')
                    ->withInput();
            }

            /*
            ==================================================
            VERIFICAR USO DEL PLAN POR ESTUDIANTES
            ==================================================
            */

            // Estudiantes activos actualmente
            $tieneEstudiantesActivos = $plan->estudiantes()
                ->where('idEstatus', 1)
                ->exists();

            // Historial (estudiantes que ya no están activos)
            $tieneHistorial = $plan->estudiantes()
                ->where('idEstatus', '!=', 1)
                ->exists();

            /*
            🔴 CASO 1
            Tiene estudiantes activos Y también historial
            ➜ No se puede modificar nada
            */
            if ($tieneEstudiantesActivos && $tieneHistorial) {
                return redirect()
                    ->route('consultaPlan')
                    ->with(
                        'popupError',
                        'No se puede modificar este plan de pago porque tiene estudiantes asignados actualmente y también cuenta con historial. Solo puede ser suspendido.'
                    );
            }

            /*
            🟡 CASO 2
            Tiene estudiantes activos pero SIN historial
            ➜ Solo se permite cambiar el nombre
            */
            if ($tieneEstudiantesActivos && !$tieneHistorial) {
                $plan->nombrePlanDePago = $nombreFormateado;
                $plan->save();

                return redirect()
                    ->route('consultaPlan')
                    ->with(
                        'success',
                        'El nombre del plan se actualizó correctamente. No se pueden modificar los conceptos porque el plan tiene estudiantes asignados.'
                    );
            }

            /*
            🟢 CASO 3
            No tiene estudiantes activos
            ➜ Se permite modificar nombre y conceptos
            */

            $request->validate([
                'cantidades' => 'required|array',
            ]);

            $todasCero = collect($request->cantidades)
                ->every(fn ($c) => intval($c) <= 0);

            if ($todasCero) {
                return back()
                    ->with('popupError', 'Debes seleccionar al menos un concepto.')
                    ->withInput();
            }

            // Actualizar nombre
            $plan->nombrePlanDePago = $nombreFormateado;
            $plan->save();

            // Reemplazar conceptos
            $plan->conceptos()->delete();

            foreach ($request->cantidades as $idConcepto => $cantidad) {
                $cantidad = intval($cantidad);
                if ($cantidad > 0) {
                    PlanConcepto::create([
                        'idPlanDePago'     => $plan->idPlanDePago,
                        'idConceptoDePago' => $idConcepto,
                        'cantidad'         => $cantidad
                    ]);
                }
            }

            return redirect()
                ->route('consultaPlan')
                ->with('success', 'Plan de pagos actualizado correctamente.');

        } catch (\Throwable $e) {

            // report($e); // ← opcional para logs

            return back()
                ->with('popupError', 'Ocurrió un error al actualizar el plan de pagos. Intenta nuevamente.')
                ->withInput();
        }
    }





    public function destroy($id)
    {
        try {

            $plan = PlanDePago::findOrFail($id);

            // ==============================
            // VERIFICAR SI ALGÚN ESTUDIANTE TIENE O TUVO ESTE PLAN
            // ==============================
            $tieneHistorial = $plan->estudiantes()->exists();

            if ($tieneHistorial) {
                return back()->with(
                    'popupError',
                    'No se puede eliminar este plan de pago porque existen o existieron estudiantes con este plan asignado. Solo puede ser suspendido si no existen estudiantes con este plan asignado en este momento.'
                );
            }

            // ==============================
            // ELIMINAR CONCEPTOS ASOCIADOS
            // ==============================
            $plan->conceptos()->delete();

            // ==============================
            // ELIMINAR PLAN
            // ==============================
            $plan->delete();

            return back()->with('success', 'Plan eliminado correctamente.');

        } catch (\Throwable $e) {

            return back()->with(
                'popupError',
                'Ocurrió un error al intentar eliminar el plan de pago. Intenta nuevamente.'
            );

            // Para depuración:
            // report($e);
        }
    }






    public function asignarCreate(Request $request)
    {
        $buscar = $request->buscar;
        $filtro = $request->filtro;
        $orden  = $request->orden;

        // =============================
        // PLANES DE PAGO ACTIVOS
        // =============================
        $planes = PlanDePago::where('idEstatus', 1)->get();

        // =============================
        // QUERY BASE DE ESTUDIANTES
        // =============================
        $query = Estudiante::with([
            'usuario',
            'planDeEstudios.licenciatura'
        ]);

        // =============================
        // BUSCADOR (nombre completo o matrícula)
        // =============================
        if ($request->filled('buscar')) {

            $buscar = trim($buscar);

            $query->where(function ($q) use ($buscar) {

                // Buscar por matrícula
                $q->where('matriculaAlfanumerica', 'LIKE', "%{$buscar}%");

                // Buscar por nombre completo
                $q->orWhereHas('usuario', function ($u) use ($buscar) {
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
            });
        }

        // =============================
        // FILTRO POR ESTATUS ACADÉMICO
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
            'SGFIDMA.moduloPlanDePago.asignacionDePlanDePago',
            compact('planes', 'estudiantes', 'buscar', 'filtro', 'orden')
        );
    }


    private function obtenerMesesDelPlan(Carbon $inicio): array
    {
        $meses = [];

        for ($i = 0; $i < 6; $i++) {
            $meses[] = $inicio->copy()->addMonths($i);
        }

        return $meses;
    }

    private function generarReferenciaBancaria(
        Estudiante $estudiante,
        ConceptoDePago $concepto,
        float $monto,
        Carbon $fechaLimite
    ) {
        $anioBase = 2013;
        $anioCond = ($fechaLimite->year - $anioBase) * 372;
        $mesCond  = ($fechaLimite->month - 1) * 31;
        $diaCond  = ($fechaLimite->day - 1);
        $fechaCondensada = $anioCond + $mesCond + $diaCond;

        $prefijo   = '0007777';
        $matricula = $estudiante->matriculaNumerica;

        $conceptoFormateado = str_pad(
            $concepto->idConceptoDePago,
            2,
            '0',
            STR_PAD_LEFT
        );

        $monto = number_format($monto, 2, '', '');
        $monto = str_pad($monto, 10, '0', STR_PAD_LEFT);

        $ponderadores = [7, 3, 1];
        $digitos = str_split($monto);
        $suma = 0;

        foreach ($digitos as $i => $digito) {
            $indice = (count($digitos) - 1 - $i) % 3;
            $suma += ((int)$digito) * $ponderadores[$indice];
        }

        $importeCondensado = $suma % 10;
        $constante = '2';

        $referenciaInicial =
            $prefijo .
            $matricula .
            $conceptoFormateado .
            $fechaCondensada .
            $importeCondensado .
            $constante;

        $ponderadores97 = [11, 13, 17, 19, 23];
        $digitosRef = str_split($referenciaInicial);
        $suma = 0;

        foreach ($digitosRef as $i => $digito) {
            $pos = (count($digitosRef) - 1 - $i) % count($ponderadores97);
            $suma += ((int)$digito) * $ponderadores97[$pos];
        }

        $remanente = str_pad(($suma % 97) + 1, 2, '0', STR_PAD_LEFT);

        return $referenciaInicial . $remanente;
    }




    public function asignarStore(Request $request)
    {
        $hoy = Carbon::now();
        $mesActual = $hoy->month;

        // =============================
        // RESTRICCIÓN DE MESES
        // =============================
        if (!in_array($mesActual, [3, 9])) {
            return back()
                ->with('popupError', 'Los planes de pago solo se pueden asignar en los meses de MARZO y SEPTIEMBRE.')
                ->withInput();
        }

        // =============================
        // VALIDACIÓN
        // =============================
        $validator = Validator::make(
            $request->all(),
            [
                'idPlanDePago'        => 'required|exists:Plan_de_pago,idPlanDePago',
                'fechaDeFinalizacion' => 'required|date|after_or_equal:today',
                'estudiantes'         => 'required|array|min:1',
                'estudiantes.*'       => 'exists:Estudiante,idEstudiante',
            ],
            [
                'required'        => 'El campo :attribute es obligatorio.',
                'exists'          => 'El :attribute seleccionado no es válido.',
                'array'           => 'Debe seleccionar al menos un estudiante.',
                'min'             => 'Debe seleccionar al menos :min estudiante.',
                'date'            => 'El campo :attribute debe ser una fecha válida.',
                'after_or_equal'  => 'La :attribute no puede ser menor a la fecha actual.',
            ],
            [
                'idPlanDePago'        => 'plan de pago',
                'fechaDeFinalizacion' => 'fecha de finalización',
                'estudiantes'         => 'estudiantes',
            ]
        );

        if ($validator->fails()) {
            return back()
                ->with('popupError', 'No se pudo asignar el plan. Verifica la información.')
                ->withErrors($validator)
                ->withInput();
        }


        $creados = [];
        $duplicados = [];
        $noAplicados = [];


        foreach ($request->estudiantes as $idEstudiante) {

            $usuario = Estudiante::with('usuario')
                ->find($idEstudiante)
                ->usuario;

            $nombreCompleto = trim(
                collect([
                    $usuario->primerNombre,
                    $usuario->segundoNombre,
                    $usuario->primerApellido,
                    $usuario->segundoApellido,
                ])->filter()->implode(' ')
            );

            $estudiante = Estudiante::with(['usuario'])
                ->findOrFail($idEstudiante);

            // 🔹 Inicializar SIEMPRE
            $creados[$idEstudiante]['estudiante']    = $nombreCompleto;
            $duplicados[$idEstudiante]['estudiante'] = $nombreCompleto;

            $creados[$idEstudiante]['pagos']    = [];
            $duplicados[$idEstudiante]['pagos'] = [];

            $seCrearonPagos = false;

            $estudiantePlan = EstudiantePlan::where('idEstudiante', $idEstudiante)
                ->where('idEstatus', 1)
                ->with('planDePago')
                ->first();

            // =============================
            // VALIDAR PLAN ACTIVO EXISTENTE
            // =============================
            if ($estudiantePlan) {

                $noAplicados[] = [
                    'estudiante' => $nombreCompleto,
                    'motivo'     => 'El estudiante ya cuenta con un plan de pago activo: ' .
                                    $estudiantePlan->planDePago->nombrePlanDePago
                ];

                continue; 
            }


            
            $plan = PlanDePago::with('conceptos.concepto')->findOrFail($request->idPlanDePago);

            $contieneInscripcion   = $plan->conceptos->contains(fn ($pc) => $pc->concepto->idConceptoDePago == 1);
            $contieneReinscripcion = $plan->conceptos->contains(fn ($pc) => $pc->concepto->idConceptoDePago == 30);

            $grado = $estudiante->grado;


            if ($grado == 1 && $contieneReinscripcion) {

                $noAplicados[] = [
                    'estudiante' => $nombreCompleto,
                    'motivo'     => 'No se aplicó el plan porque el estudiante es de nuevo ingreso y el plan contiene reinscripción.'
                ];

                continue;
            }

            if ($grado >= 2 && $contieneInscripcion) {

                $noAplicados[] = [
                    'estudiante' => $nombreCompleto,
                    'motivo'     => 'No se aplicó el plan porque el estudiante no es de nuevo ingreso y el plan contiene inscripción.'
                ];

                continue; 
            }





            if (!$estudiantePlan) {
                $estudiantePlan = EstudiantePlan::create([
                    'idEstudiante'        => $idEstudiante,
                    'idPlanDePago'        => $request->idPlanDePago,
                    'idEstatus'           => 1,
                    'fechaDeAsignacion'   => now()->toDateString(),
                    'fechaDeFinalizacion' => $request->fechaDeFinalizacion,
                ]);
            }



            $plan = PlanDePago::with('conceptos.concepto')->findOrFail($request->idPlanDePago);
            // Determinar mes inicial
            $hoy = Carbon::now();
            $inicioPlan = $hoy->month <= 6
                ? Carbon::create($hoy->year, 3, 1)
                : Carbon::create($hoy->year, 9, 1);

            $meses = $this->obtenerMesesDelPlan($inicioPlan);

            foreach ($plan->conceptos as $pc) {

                $concepto = $pc->concepto;

                // =====================
                // INSCRIPCIÓN o REINSCRIPCION (1 VEZ)
                // =====================
                if (in_array($concepto->idConceptoDePago, [1, 30])) {

                    $primerMes = $meses[0];
                    $fechaLimite = $primerMes->copy()->day(15);

                    $aportacionTexto = $concepto->idConceptoDePago == 1
                        ? 'INSCRIPCIÓN'
                        : 'REINSCRIPCIÓN';

                    $referencia = $this->generarReferenciaBancaria(
                        $estudiantePlan->estudiante,
                        $concepto,
                        $concepto->costo,
                        $fechaLimite
                    );

                    $pagoExistente = Pago::where('Referencia', $referencia)
                        ->where('idEstudiante', $idEstudiante)
                        ->first();

                    if (!$pagoExistente) {

                        $seCrearonPagos = true;

                        Pago::create([
                            'Referencia'            => $referencia,
                            'idEstudiante'          => $idEstudiante,
                            'idConceptoDePago'      => $concepto->idConceptoDePago,
                            'montoAPagar'           => $concepto->costo,
                            'fechaGeneracionDePago' => $primerMes->copy()->day(1),
                            'fechaLimiteDePago'     => $fechaLimite,
                            'aportacion'            => $aportacionTexto,
                            'idEstatus'             => 3
                        ]);

                        $creados[$idEstudiante]['pagos'][] = [
                            'referencia' => $referencia,
                            'concepto'   => $aportacionTexto,
                            'fecha'      => $fechaLimite,
                            'idConcepto' => $concepto->idConceptoDePago, // 🔑 CLAVE
                        ];

                    } else {

                        $duplicados[$idEstudiante]['pagos'][] = [
                            'referencia' => $pagoExistente->Referencia,
                            'concepto'   => $pagoExistente->aportacion ?? $aportacionTexto,
                            'fecha'      => $pagoExistente->fechaLimiteDePago,
                            'idConcepto' => $concepto->idConceptoDePago,
                        ];
                    }
                }



                // =====================
                // MENSUALIDADES
                // =====================
                if ($concepto->idConceptoDePago == 2) {

                    $contadorMes = 1;

                    foreach ($meses as $mes) {

                        $fechaGeneracion = $mes->copy()->day(1);
                        $fechaLimite     = $mes->copy()->day(15);

                        // =============================
                        // MONTO (BECAS SOLO DE 2ª A 6ª)
                        // =============================
                        $montoFinal = $concepto->costo;

                        if ($contadorMes > 1) {

                            $solicitudBeca = $estudiantePlan->estudiante
                                ->solicitudesDeBeca()
                                ->where('idEstatus', 6)
                                ->with('beca')
                                ->first();

                            if ($solicitudBeca && $solicitudBeca->beca) {
                                $porcentaje = $solicitudBeca->beca->porcentajeDeDescuento;
                                $montoFinal -= ($concepto->costo * $porcentaje) / 100;
                            }
                        }

                        // =============================
                        // REFERENCIA
                        // =============================
                        $referencia = $this->generarReferenciaBancaria(
                            $estudiantePlan->estudiante,
                            $concepto,
                            $montoFinal,
                            $fechaLimite
                        );

                        // =============================
                        // VALIDAR DUPLICADO (IGUAL QUE INSCRIPCIÓN)
                        // =============================
                        $pagoExistente = Pago::where('Referencia', $referencia)
                            ->where('idEstudiante', $idEstudiante)
                            ->first();

                        if (!$pagoExistente) {

                            $seCrearonPagos = true;

                            Pago::create([
                                'Referencia'            => $referencia,
                                'idEstudiante'          => $idEstudiante,
                                'idConceptoDePago'      => $concepto->idConceptoDePago,
                                'montoAPagar'           => $montoFinal,
                                'fechaGeneracionDePago' => $fechaGeneracion,
                                'fechaLimiteDePago'     => $fechaLimite,
                                'aportacion'            => 'MES DE ' . strtoupper(
                                    $mes->locale('es')->translatedFormat('F')
                                ),
                                'idEstatus'             => 3
                            ]);

                            $usuario = $estudiantePlan->estudiante->usuario;

                            $creados[$idEstudiante]['pagos'][] = [
                                'referencia' => $referencia,
                                'concepto'   => 'MES DE ' . strtoupper(
                                    $mes->locale('es')->translatedFormat('F')
                                ),
                                'fecha'      => $fechaLimite,
                                'idConcepto' => 2,
                            ];

                        } else {

                            $usuario = $estudiantePlan->estudiante->usuario;

                            $duplicados[$idEstudiante]['pagos'][] = [
                                'referencia' => $pagoExistente->Referencia,
                                'concepto'   => $pagoExistente->aportacion ?? $pagoExistente->concepto->nombreConceptoDePago,
                                'fecha'      => $pagoExistente->fechaLimiteDePago,
                                'idConcepto' => $concepto->idConceptoDePago,
                            ];
                        }

                        $contadorMes++;
                    }
                }


            }




            // =============================
            // NOTIFICACIÓN AL ESTUDIANTE
            // =============================
            if ($seCrearonPagos) {

                $estudiante = $estudiantePlan->estudiante()->with('usuario')->first();

                Notificacion::create([
                    'idUsuario'          => $estudiante->idUsuario,
                    'titulo'             => 'Nuevo plan de pago asignado',
                    'mensaje'            => "Se te ha asignado el plan de pago: {$estudiantePlan->planDePago->nombrePlanDePago}. Revisa tu información en inicio.",
                    'tipoDeNotificacion' => 1,
                    'fechaDeInicio'      => now()->toDateString(),
                    'fechaFin'           => now()->addDays(3)->toDateString(),
                    'leida'              => 0,
                ]);
            }



            // =============================
            // ORDENAR PAGOS POR FECHA
            // =============================
            if (!empty($creados[$idEstudiante]['pagos'])) {
                usort(
                    $creados[$idEstudiante]['pagos'],
                    fn ($a, $b) => strtotime($a['fecha']) <=> strtotime($b['fecha'])
                );
            }

            if (!empty($duplicados[$idEstudiante]['pagos'])) {
                usort(
                    $duplicados[$idEstudiante]['pagos'],
                    fn ($a, $b) => strtotime($a['fecha']) <=> strtotime($b['fecha'])
                );
            }

        }

        return redirect()
            ->route('planPago.detallesAsignacion')
            ->with('successAsignacion', true)
            ->with('creados', $creados)
            ->with('duplicados', $duplicados)
            ->with('noAplicados', $noAplicados);
    }



    public function detallesAsignacionDePlan()
    {
        return view('SGFIDMA.moduloPlanDePago.detallesAsignacionDePlan', [
            'creados'    => session('creados', []),
            'duplicados' => session('duplicados', []),
            'noAplicados'  => session('noAplicados', []),
        ]);
    }





}


