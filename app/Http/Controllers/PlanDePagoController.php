<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $request->validate([
            'nombrePlan' => 'required|string|max:150',
            'cantidades' => 'required|array',
        ]);

        $existe = PlanDePago::whereRaw('LOWER(nombrePlanDePago) = ?', [mb_strtolower($request->nombrePlan)])
                        ->exists();

        if ($existe) {
            return back()
                ->with('popupError', 'Ya existe un plan de pago con ese nombre.')
                ->withInput();
        }


        $nombre = $this->mbUcwords($request->nombrePlan);

        if ($request->cantidades) {
            // Verificar si todos los inputs son 0
            $todasCero = collect($request->cantidades)->every(function($cantidad) {
                return intval($cantidad) <= 0;
            });

            if ($todasCero) {
                return back()
                    ->with('popupError', 'El plan de pago debe tener conceptos seleccionados.')
                    ->withInput();
            }


        

            // Crear el plan de pago
            $plan = PlanDePago::create([
                'nombrePlanDePago' =>$nombre,
                'idEstatus' => 1
            ]);



            // Recorrer los inputs de cantidades
            if ($request->cantidades) {
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
            }
        }

        return redirect()->route('altaPlan')->with('success', 'Plan de pago creado correctamente.');
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

}


