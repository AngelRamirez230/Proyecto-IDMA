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



    public function asignarStore(Request $request)
    {
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


        foreach ($request->estudiantes as $idEstudiante) {

            // Evitar duplicados activos
            $existe = EstudiantePlan::where('idEstudiante', $idEstudiante)
                ->where('idEstatus', 1)
                ->exists();

            if ($existe) {
                continue; // salta este estudiante
            }

            // =============================
            // GUARDAR PLAN ASIGNADO
            // =============================
            $estudiantePlan = EstudiantePlan::create([
                'idEstudiante'        => $idEstudiante,
                'idPlanDePago'        => $request->idPlanDePago,
                'idEstatus'           => 1, // Activo
                'fechaDeAsignacion'   => Carbon::now()->toDateString(),
                'fechaDeFinalizacion' => $request->fechaDeFinalizacion,
            ]);

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
            ->route('consultaPlan')
            ->with('success', 'Plan de pago asignado correctamente.');
    }






}


