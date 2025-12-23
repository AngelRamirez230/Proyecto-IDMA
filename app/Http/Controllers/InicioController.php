<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\GeneracionController;

class InicioController extends Controller
{
    public function index()
    {
        $datosGeneracion = app(\App\Http\Controllers\GeneracionController::class)->verificarGeneracion();

        return view('layouts.inicio', [
            'datosGeneracion' => $datosGeneracion
        ]);
    }
}
