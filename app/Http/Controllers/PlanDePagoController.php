<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\PlanDePago;
use App\Models\PlanConcepto;
use App\Models\ConceptoDePago;

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




}


