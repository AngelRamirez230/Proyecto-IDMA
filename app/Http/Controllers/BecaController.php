<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beca;

class BecaController extends Controller
{
    public function create()
    {
        return view('SGFIDMA.moduloBecas.altaDeBeca');
    }

    public function store(Request $request)
    {
        // Validaciones básicas
        $request->validate([
            'nombreBeca' => 'required|string',
            'porcentajeBeca' => 'required|numeric|min:1|max:100',
        ]);

        // Validar si ya existe un registro idéntico
        $existe = Beca::where('nombreDeBeca', $request->nombreBeca)
                      ->where('porcentajeDeDescuento', $request->porcentajeBeca)
                      ->exists();

        if ($existe) {
            return back()
                ->with('popupError', 'Ya existe una beca con ese nombre y porcentaje.')
                ->withInput();
        }

        // Crear registro
        Beca::create([
            'nombreDeBeca' => $request->nombreBeca,
            'porcentajeDeDescuento' => $request->porcentajeBeca,
            'idEstatus' => 1
        ]);

        return redirect()->route('altaBeca')
                         ->with('success', 'Beca registrada correctamente');
        

    }

     public function index()
    {
        // Obtener todas las becas con su estatus
        $becas = Beca::with('estatus')->get();

        return view('SGFIDMA.moduloBecas.consultaDeBeca', compact('becas'));
    }

    public function edit($id)
    {
        // Buscar la beca por id
        $beca = Beca::findOrFail($id);

        // Retornar la vista de modificación con los datos de la beca
        return view('SGFIDMA.moduloBecas.modificacionDeBeca', compact('beca'));
    }


    
}
