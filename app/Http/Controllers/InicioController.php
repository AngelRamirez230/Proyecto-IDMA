<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\GeneracionController;
use App\Models\Notificacion;
use App\Models\EstudiantePlan;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $horariosEstudiante = collect();

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

            $horariosEstudiante = DB::table('Grupo_estudiante as ge')
                ->join('Grupo as g', 'ge.idGrupo', '=', 'g.idGrupo')
                ->join('Horario as h', 'h.idGrupo', '=', 'g.idGrupo')
                ->join('Asignatura as a', 'h.idAsignatura', '=', 'a.idAsignatura')
                ->leftJoin('Aula as au', 'h.idAula', '=', 'au.idAula')
                ->join('horario_dia_rango_horario as hdrh', 'h.idHorario', '=', 'hdrh.Horario_idHorario')
                ->join('Dia_semana as d', 'hdrh.Dia_semana_idDiaSemana', '=', 'd.idDiaSemana')
                ->join('Rango_de_horario as r', 'hdrh.Rango_de_horario', '=', 'r.idRangoDeHorario')
                ->select(
                    'a.nombre as asignatura',
                    'g.claveGrupo',
                    'au.nombreAula',
                    'd.nombreDia',
                    'r.horaInicio',
                    'r.horaFin'
                )
                ->where('ge.idEstudiante', $usuario->estudiante->idEstudiante)
                ->orderBy('d.idDiaSemana')
                ->orderBy('r.horaInicio')
                ->get();
        }

        return view('layouts.inicio', [
            'datosGeneracion' => $datosGeneracion,
            'notificaciones'  => $notificaciones,
            'planAsignado'    => $planAsignado,
            'pagos'           => $pagos,
            'horariosEstudiante' => $horariosEstudiante
        ]);
    }
}
