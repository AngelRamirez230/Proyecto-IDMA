<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoDeUnidad;

class ConceptoController extends Controller
{
    public function create()
    {
        $unidades = TipoDeUnidad::all();  // ← trae todas las unidades
        return view('SGFIDMA.moduloConceptosDePago.altaDeConcepto', compact('unidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombreConcepto' => 'required|string|max:255',
            'costo' => 'required|numeric',
            'unidad' => 'required|numeric',
        ]);

        \DB::table('concepto_de_pago')->insert([
            'nombreConceptoDePago' => $request->nombreConcepto,
            'costo' => $request->costo,
            'idUnidad' => $request->unidad,
            'idEstatus' => 1, // si tienes estatus
        ]);

        return redirect()->back()->with('success', 'Concepto registrado correctamente');
    }
}
