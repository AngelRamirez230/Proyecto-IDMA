<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beca;

class BecaController extends Controller
{

    public function index()
    {
        return view('SGFIDMA.moduloBecas.consultaDeBeca');
    }

    public function store(Request $request)
    {
        Beca::create([
            'nombreDeBeca' => $request->nombreBeca,
            'porcentajeDeDescuento' => $request->porcentajeBeca,
            'idEstatus' => 1
        ]);

        return redirect()->route('consultaBeca')->with('success', 'Beca guardada correctamente');
    }
}
