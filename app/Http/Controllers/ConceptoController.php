<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoDeUnidad;
use App\Models\ConceptoDePago;

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
        $request->validate([
            'nombreConcepto' => 'required|string|max:150',
            'costo' => 'required|numeric|min:1',
            'unidad' => 'required|numeric',
        ]);

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

        return redirect()->back()->with('success', 'Concepto registrado correctamente');
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


    public function index()
    {
        // Trae todos los conceptos con sus relaciones
        $conceptos = ConceptoDePago::with(['unidad', 'estatus'])->get();

        // Retorna la vista
        return view('SGFIDMA.moduloConceptosDePago.consultaDeConceptos', compact('conceptos'));
    }
}
