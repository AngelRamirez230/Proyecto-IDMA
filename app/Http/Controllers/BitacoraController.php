<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $bitacoras = Bitacora::with(['usuarioResponsable', 'usuarioAfectado'])
            ->orderByDesc('fecha')
            ->paginate(15);

        return view('shared.moduloReportes.consultaDeBitacora', compact('bitacoras'));
    }
}
