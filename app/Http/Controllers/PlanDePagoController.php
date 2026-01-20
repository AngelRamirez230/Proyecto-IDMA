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
        $conceptos = ConceptoDePago::all();
        return view('SGFIDMA.moduloPlanDePago.altaPlan', compact('conceptos'));
    }

    public function store(Request $request)
    {
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

        // ⛔ Si falla la validación
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
        // CREAR PLAN DE PAGO
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
                    'idPlanDePago' => $plan->idPlanDePago,
                    'idConceptoDePago' => $idConcepto,
                    'cantidad' => $cantidad
                ]);
            }
        }

        return redirect()
            ->route('altaPlan')
            ->with('success', 'Plan de pagos creado correctamente.');
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
        $buscar = $request->buscarPlan;
        $filtro = $request->filtro;
        $orden  = $request->orden;

        $plan = PlanDePago::with('estatus');

        
        if (!empty($buscar)) {
            $plan->where('nombrePlanDePago', 'LIKE', '%' . $buscar . '%');
        }

        
        if ($filtro == 'activas') {
            $plan->where('idEstatus', 1);
        } elseif ($filtro == 'suspendidas') {
            $plan->where('idEstatus', 2);
        }

        
        if ($orden == 'alfabetico') {
            $plan->orderBy('nombrePlanDePago', 'ASC');
        }

        
        $planes = $plan->paginate(5)->withQueryString();

        return view('SGFIDMA.moduloPlanDePago.consultaPlanDePago', compact('planes', 'buscar', 'filtro', 'orden'));
    }


    public function edit($id)
    {
        $plan = PlanDePago::with('conceptos')->findOrFail($id);

        // Obtener todos los conceptos activos
        $conceptos = ConceptoDePago::where('idEstatus', 1)->get();

        // Convertir cantidades actuales en un arreglo [idConcepto => cantidad]
        $cantidadesActuales = $plan->conceptos->pluck('cantidad', 'idConceptoDePago')->toArray();

        return view('SGFIDMA.moduloPlanDePago.modificacionPlan', compact('plan', 'conceptos', 'cantidadesActuales'));
    }

    public function update(Request $request, $id)
    {
        $plan = PlanDePago::findOrFail($id);

        // Si solo cambia estatus
        if ($request->accion === 'Suspender/Habilitar') {

            $estatusAnterior = $plan->idEstatus;
            $plan->idEstatus = ($plan->idEstatus == 1) ? 2 : 1;
            $plan->save();

            $mensaje = ($estatusAnterior == 1)
                ? "El plan de pago {$plan->nombrePlanDePago} ha sido suspendido."
                : "El plan de pago {$plan->nombrePlanDePago} ha sido activado.";

            return redirect()->route('consultaPlan')->with('success', $mensaje);
        }

        // Validación normal
        $request->validate([
            'nombrePlan' => 'required|string|max:150',
            'cantidades' => 'required|array',
        ]);

        // Nombre formateado
        $nombreFormateado = $this->mbUcwords($request->nombrePlan);

        // Verificar duplicado sin contar el actual
        $existe = PlanDePago::whereRaw('LOWER(nombrePlanDePago) = ?', [mb_strtolower($request->nombrePlan)])
                            ->where('idPlanDePago', '!=', $id)
                            ->exists();

        if ($existe) {
            return back()
                ->with('popupError', 'Ya existe un plan de pago con ese nombre.')
                ->withInput();
        }

        // Verificar que no todos sean 0
        $todasCero = collect($request->cantidades)->every(fn($c) => intval($c) <= 0);

        if ($todasCero) {
            return back()
                ->with('popupError', 'Debes seleccionar al menos un concepto.')
                ->withInput();
        }

        // Eliminar conceptos anteriores
        $plan->conceptos()->delete();

        // Registrar nuevos
        foreach ($request->cantidades as $idConcepto => $cantidad) {
            $cantidad = intval($cantidad);

            if ($cantidad > 0) {
                PlanConcepto::create([
                    'idPlanDePago' => $plan->idPlanDePago,
                    'idConceptoDePago' => $idConcepto,
                    'cantidad' => $cantidad
                ]);
            }
        }

        return redirect()->route('consultaPlan')->with('success', 'Plan de pagos actualizado correctamente.');
    }


    public function destroy($id)
    {
        $plan = PlanDePago::findOrFail($id);

        // Verificar si el plan tiene conceptos asignados
        $estaEnUso = PlanConcepto::where('idPlanDePago', $id)->exists();


        $plan->conceptos()->delete();

        $plan->delete();

        return back()->with('success', 'Plan eliminado correctamente.');
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


        foreach ($request->estudiantes as $idEstudiante) {

            $estudiantePlan = EstudiantePlan::where('idEstudiante', $idEstudiante)
                ->where('idEstatus', 1)
                ->first();

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
                if ($concepto->idConceptoDePago == 1) {

                    $primerMes = $meses[0];



                    $referencia = $this->generarReferenciaBancaria(
                        $estudiantePlan->estudiante,
                        $concepto,
                        $concepto->costo,
                        $primerMes->copy()->day(15)
                    );

                    $pagoExistente = Pago::where('Referencia', $referencia)
                        ->where('idEstudiante', $idEstudiante)
                        ->first();


                    if (!$pagoExistente) {

                        $fechaLimite = $primerMes->copy()->day(15);

                        Pago::create([
                            'Referencia'            => $referencia,
                            'idEstudiante'          => $idEstudiante,
                            'idConceptoDePago'      => $concepto->idConceptoDePago,
                            'montoAPagar'           => $concepto->costo,
                            'fechaGeneracionDePago' => $primerMes->copy()->day(1),
                            'fechaLimiteDePago'     => $fechaLimite,
                            'aportacion'            => 'INSCRIPCIÓN',
                            'idEstatus'             => 3
                        ]);

                        $usuario = $estudiantePlan->estudiante->usuario;

                        $duplicados[$idEstudiante]['estudiante'] = trim(
                            collect([
                                $usuario->primerNombre,
                                $usuario->segundoNombre,
                                $usuario->primerApellido,
                                $usuario->segundoApellido,
                            ])->filter()->implode(' ')
                        );


                        $creados[$idEstudiante]['pagos'][] = [
                            'referencia' => $referencia,
                            'concepto'   => 'INSCRIPCION',
                            'fecha'      => $fechaLimite,
                        ];

                    } else {
                        $usuario = $estudiantePlan->estudiante->usuario;

                        $duplicados[$idEstudiante]['estudiante'] = trim(
                            collect([
                                $usuario->primerNombre,
                                $usuario->segundoNombre,
                                $usuario->primerApellido,
                                $usuario->segundoApellido,
                            ])->filter()->implode(' ')
                        );

                        $duplicados[$idEstudiante]['pagos'][] = [
                            'referencia' => $pagoExistente->Referencia,
                            'concepto'   => $pagoExistente->aportacion ?? 'INSCRIPCION',
                            'fecha'      => $pagoExistente->fechaLimiteDePago,
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

                            $creados[$idEstudiante]['estudiante'] = trim(
                                collect([
                                    $usuario->primerNombre,
                                    $usuario->segundoNombre,
                                    $usuario->primerApellido,
                                    $usuario->segundoApellido,
                                ])->filter()->implode(' ')
                            );

                            $creados[$idEstudiante]['pagos'][] = [
                                'referencia' => $referencia,
                                'concepto'   => 'MES DE ' . strtoupper(
                                    $mes->locale('es')->translatedFormat('F')
                                ),
                                'fecha'      => $fechaLimite,
                            ];

                        } else {

                            $usuario = $estudiantePlan->estudiante->usuario;

                            $duplicados[$idEstudiante]['estudiante'] = trim(
                                collect([
                                    $usuario->primerNombre,
                                    $usuario->segundoNombre,
                                    $usuario->primerApellido,
                                    $usuario->segundoApellido,
                                ])->filter()->implode(' ')
                            );

                            $duplicados[$idEstudiante]['pagos'][] = [
                                'referencia' => $pagoExistente->Referencia,
                                'concepto'   => $pagoExistente->aportacion ?? $pagoExistente->concepto->nombreConceptoDePago,
                                'fecha'      => $pagoExistente->fechaLimiteDePago,
                            ];
                        }

                        $contadorMes++;
                    }
                }


            }




            // =============================
            // CREAR NOTIFICACIÓN PARA EL ESTUDIANTE
            // =============================
            $estudiante = $estudiantePlan->estudiante()->with('usuario')->first();

            Notificacion::create([
                'idUsuario'          => $estudiante->idUsuario,
                'titulo'             => 'Nuevo plan de pago asignado',
                'mensaje'            => "Se te ha asignado el plan de pago: {$estudiantePlan->planDePago->nombrePlanDePago}. Revisa tu información de pagos.",
                'tipoDeNotificacion' => 1, // Informativo
                'fechaDeInicio'      => now()->toDateString(),
                'fechaFin'           => now()->addDays(3)->toDateString(),
                'leida'              => 0,
            ]);
        }

        return redirect()
            ->route('planPago.detallesAsignacion')
            ->with('successAsignacion', true)
            ->with('creados', $creados)
            ->with('duplicados', $duplicados);
    }



    public function detallesAsignacionDePlan()
    {
        return view('SGFIDMA.moduloPlanDePago.detallesAsignacionDePlan', [
            'creados'    => session('creados', []),
            'duplicados' => session('duplicados', []),
        ]);
    }





}


