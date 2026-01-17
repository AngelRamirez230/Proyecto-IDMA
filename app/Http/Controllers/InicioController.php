<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\GeneracionController;
use App\Models\Notificacion;
use Carbon\Carbon;

class InicioController extends Controller
{
    public function index()
    {
        // Notificaciones de pagos u otras
        $usuarioId = auth()->user()->idUsuario;

        $notificaciones = Notificacion::where('idUsuario', $usuarioId)
            ->where('fechaDeInicio', '<=', Carbon::today())   // inicio ya pasado
            ->where('fechaFin', '>=', Carbon::today())       // fin aún no pasado
            ->where('leida', 0)                              // no leída
            ->orderBy('fechaDeInicio', 'desc')
            ->get();

        // Datos de generacion (lo que ya tenías)
        $datosGeneracion = app(GeneracionController::class)->verificarGeneracion();

        return view('layouts.inicio', [
            'datosGeneracion' => $datosGeneracion,
            'notificaciones'  => $notificaciones
        ]);
    }
}
