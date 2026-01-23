<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoDeUnidad;
use App\Models\ConceptoDePago;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ConceptoController extends Controller
{
    // Mostrar formulario de alta de concepto
    public function create()
    {
        $unidades = TipoDeUnidad::all();
        return view('SGFIDMA.moduloConceptosDePago.altaDeConcepto', compact('unidades'));
    }

    // Guardar concepto
    public function store(Request $request)
    {
        
        $validator = Validator::make(
            $request->all(),
            [
                // ======================
                // CONCEPTO DE PAGO
                // ======================
                'nombreConcepto' => 'required|string|max:150',
                'costo'          => 'required|numeric|min:0|max:9999999.99',
                'unidad'         => 'required|exists:tipo_de_unidad,idTipoDeUnidad',
            ],
            [
                // ======================
                // MENSAJES GENERALES
                // ======================
                'required' => 'El campo :attribute es obligatorio.',
                'string'   => 'El campo :attribute debe ser texto.',
                'numeric'  => 'El campo :attribute debe ser un número válido.',
                'min'      => 'El campo :attribute debe ser mayor o igual a :min.',
                'max'      => 'El campo :attribute no debe exceder :max.',
                'exists'   => 'La opción seleccionada en :attribute no es válida.',
            ],
            [
                // ======================
                // NOMBRES AMIGABLES
                // ======================
                'nombreConcepto' => 'nombre del concepto de pago',
                'costo'          => 'costo',
                'unidad'         => 'unidad',
            ]
        );

        if ($validator->fails()) {
            return back()
                ->with('popupError', 'No se pudo crear el concepto de pago. Verifica los datos ingresados.')
                ->withErrors($validator)
                ->withInput();
        }

        // Convertir a tipo oración con acentos
        $nombre = $this->mbUcwords($request->nombreConcepto);

        // Validar duplicados (ignorando mayúsculas/minúsculas)
        $existe = ConceptoDePago::whereRaw('LOWER(nombreConceptoDePago) = ?', [mb_strtolower($request->nombreConcepto)])
                                 ->exists();

        if ($existe) {
            return back()
                ->with('popupError', 'Ya existe un concepto con ese nombre')
                ->withInput();
        }

        // Guardar concepto
        ConceptoDePago::create([
            'nombreConceptoDePago' => $nombre,
            'costo' => $request->costo,
            'idUnidad' => $request->unidad,
            'idEstatus' => 1,
        ]);

        return redirect()->back()->with('success', 'Concepto de pago creado correctamente');
    }

    // Convertir a tipo oración con acentos
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
        $orden = $request->orden;
        $filtro = $request->filtro;
        $buscar = $request->buscarConcepto;
        // Trae todos los conceptos con sus relaciones
        $concepto = ConceptoDePago::with(['unidad', 'estatus']);

        /*
        ==================================================
        RESTRICCIÓN PARA ESTUDIANTES
        ==================================================
        */
        if (Auth::check() && Auth::user()->idtipoDeUsuario == 4) { // ← estudiante
            $concepto->whereNotIn('idConceptoDePago', [1, 2, 30]);
        }

        //Aplicar buscar
        if ($request->filled('buscarConcepto')) {
            $concepto->where('nombreConceptoDePago', 'LIKE', '%' . $buscar . '%');
        }

        //Aplicar filtro
        if ($filtro === 'activas') {
            $concepto->where('idEstatus', 1);
        } elseif ($filtro === 'suspendidas') {
            $concepto->where('idEstatus', 2);
        } elseif ($filtro === 'todas') {

        } elseif($filtro === 'servicio'){
            $concepto->where('idUnidad', 1);

        } elseif($filtro === 'pieza'){
            $concepto->where('idUnidad', 2);
        }

        // Aplicar orden
        if ($orden === 'alfabetico') {
            $concepto->orderBy('nombreConceptoDePago', 'asc');
        } elseif ($orden === 'costo_mayor') {
            $concepto->orderBy('costo', 'desc');
        } elseif ($orden === 'costo_menor') {
            $concepto->orderBy('costo', 'asc');
        }


        $conceptos = $concepto->paginate(5)->withQueryString();

        

        // Retorna la vista
        return view('SGFIDMA.moduloConceptosDePago.consultaDeConceptos', compact('conceptos','filtro','orden','buscar'));
    }


    public function edit($idConceptoDePago)
    {
        // Buscar el concepto por su ID
        $concepto = ConceptoDePago::findOrFail($idConceptoDePago);

        // Traer unidades para el select
        $unidades = TipoDeUnidad::all();

        return view('SGFIDMA.moduloConceptosDePago.modificacionConcepto', compact('concepto', 'unidades'));
    }


    public function update(Request $request, $idConceptoDePago)
    {
        // Buscar el concepto
        $concepto = ConceptoDePago::findOrFail($idConceptoDePago);

        if ($request->accion === 'guardar') {

            $validator = Validator::make(
                $request->all(),
                [
                    // ======================
                    // CONCEPTO DE PAGO
                    // ======================
                    'costo'          => 'required|numeric|min:0|max:9999999.99',
                    'unidad'         => 'required|exists:tipo_de_unidad,idTipoDeUnidad',
                ],
                [
                    // ======================
                    // MENSAJES GENERALES
                    // ======================
                    'required' => 'El campo :attribute es obligatorio.',
                    'string'   => 'El campo :attribute debe ser texto.',
                    'numeric'  => 'El campo :attribute debe ser un número válido.',
                    'min'      => 'El campo :attribute debe ser mayor o igual a :min.',
                    'max'      => 'El campo :attribute no debe exceder :max.',
                    'exists'   => 'La opción seleccionada en :attribute no es válida.',
                ],
                [
                    // ======================
                    // NOMBRES AMIGABLES
                    // ======================
                    'costo'          => 'costo',
                    'unidad'         => 'unidad',
                ]
            );

            if ($validator->fails()) {
                return back()
                    ->with('popupError', 'No se pudo actualizar el concepto de pago. Verifica los datos ingresados.')
                    ->withErrors($validator)
                    ->withInput();
            }




            // Guardar cambios
            $concepto->costo = $request->costo;
            $concepto->idUnidad = $request->unidad; 
            $concepto->save();

            return redirect()->route('consultaConcepto')->with('success', 'Concepto de pago actualizado correctamente.');
        }

        elseif ($request->accion === 'Suspender/Habilitar') {

            // Guardar estatus anterior
            $estatusAnterior = $concepto->idEstatus;

            $estaEnUso = \App\Models\PlanConcepto::where('idConceptoDePago', $idConceptoDePago)->exists();

            if ($estaEnUso) {
                return redirect()
                    ->route('consultaConcepto')
                    ->with('popupError', "El concepto {$concepto->nombreConceptoDePago} no puede suspenderse porque está siendo usado en un plan de pago.");
            }


            // Alternar estatus
            $concepto->idEstatus = ($concepto->idEstatus == 1) ? 2 : 1;
            $concepto->save();

            // Mensaje según acción
            $mensaje = ($estatusAnterior == 1)
                ? "El concepto {$concepto->nombreConceptoDePago} ha sido suspendido."
                : "El concepto {$concepto->nombreConceptoDePago} ha sido activado.";

            return redirect()->route('consultaConcepto')->with('success', $mensaje);
        }
    }

    public function destroy($idConceptoDePago)
    {
        // Buscar el concepto
        $concepto = ConceptoDePago::findOrFail($idConceptoDePago);

        // Verificar si está siendo usado en algún plan de pago
        $estaEnUso = \App\Models\PlanConcepto::where('idConceptoDePago', $idConceptoDePago)->exists();

        if ($estaEnUso) {
            return redirect()
                ->route('consultaConcepto')
                ->with('popupError', "El concepto {$concepto->nombreConceptoDePago} no puede eliminarse porque está siendo usado en un plan de pago.");
        }

        // Si no está en uso, eliminarlo
        $concepto->delete();

        return redirect()
            ->route('consultaConcepto')
            ->with('success', "El concepto {$concepto->nombreConceptoDePago} ha sido eliminado.");
    }



}
