<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\GeneracionController;
use App\Models\Notificacion;
use App\Models\EstudiantePlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InicioController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        // =========================
        // NOTIFICACIONES
        // =========================
        $notificaciones = Notificacion::where('idUsuario', $usuario->idUsuario)
            ->where('fechaDeInicio', '<=', Carbon::today())
            ->where('fechaFin', '>=', Carbon::today())
            ->where('leida', 0)
            ->orderBy('fechaDeInicio', 'desc')
            ->get();

        // =========================
        // DATOS DE GENERACIÓN (ADMIN)
        // =========================
        $datosGeneracion = app(GeneracionController::class)->verificarGeneracion();

        // =========================
        // PLAN DE PAGO DEL ESTUDIANTE
        // =========================
        $planAsignado = null;

        if ($usuario->estudiante) {
            $planAsignado = EstudiantePlan::with([
                    'planDePago.conceptos.concepto',
                    'estatus'
                ])
                ->where('idEstudiante', $usuario->estudiante->idEstudiante)
                ->where('idEstatus', 1) // Plan activo
                ->first();
        }

        return view('layouts.inicio', [
            'datosGeneracion' => $datosGeneracion,
            'notificaciones'  => $notificaciones,
            'planAsignado'    => $planAsignado
        ]);
    }
}
