<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\GeneracionController;
use App\Models\Notificacion;
use App\Models\EstudiantePlan;
use App\Models\Pago;
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
        // PLAN DE PAGO ACTIVO DEL ESTUDIANTE
        // =========================
        $planAsignado = null;
        $pagos = collect();

        if ($usuario->estudiante) {

            // Plan activo
            $planAsignado = EstudiantePlan::with([
                    'planDePago.conceptos.concepto',
                    'estatus'
                ])
                ->where('idEstudiante', $usuario->estudiante->idEstudiante)
                ->where('idEstatus', 1) // ACTIVO
                ->first();

            // Pagos reales generados
            $pagos = Pago::with([
                    'concepto',
                    'estatus'
                ])
                ->where('idEstudiante', $usuario->estudiante->idEstudiante)
                ->orderBy('fechaGeneracionDePago')
                ->get();
        }

        return view('layouts.inicio', [
            'datosGeneracion' => $datosGeneracion,
            'notificaciones'  => $notificaciones,
            'planAsignado'    => $planAsignado,
            'pagos'           => $pagos
        ]);
    }
}
