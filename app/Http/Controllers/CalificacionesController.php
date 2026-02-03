<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalificacionesController extends Controller
{
    private function validarAcceso()
    {
        $usuario = Auth::user();

        if (
            !$usuario ||
            !($usuario->esAdmin() || $usuario->esEmpleadoDe([2, 3, 4, 5, 6, 7]))
        ) {
            abort(403, 'No autorizado');
        }
    }

    public function index(Request $request)
    {
        $this->validarAcceso();

        $buscar = $request->input('buscarHorario');
        $orden = $request->input('orden');
        $dia = $request->input('dia');

        $query = DB::table('Horario as h')
            ->join('Asignatura as a', 'h.idAsignatura', '=', 'a.idAsignatura')
            ->join('Grupo as g', 'h.idGrupo', '=', 'g.idGrupo')
            ->leftJoin('Docente as d', 'h.idDocente', '=', 'd.idDocente')
            ->leftJoin('Usuario as u', 'd.idUsuario', '=', 'u.idUsuario')
            ->leftJoin('Aula as au', 'h.idAula', '=', 'au.idAula')
            ->join('Bloque as b', 'h.idBloque', '=', 'b.idBloque')
            ->join('Ciclo_modalidad as cm', 'b.idCicloModalidad', '=', 'cm.idCicloModalidad')
            ->join('Modalidad as m', 'cm.idModalidad', '=', 'm.idModalidad')
            ->leftJoin('horario_dia_rango_horario as hdrh', 'h.idHorario', '=', 'hdrh.Horario_idHorario')
            ->leftJoin('Dia_semana as ds', 'hdrh.Dia_semana_idDiaSemana', '=', 'ds.idDiaSemana')
            ->leftJoin('Rango_de_horario as r', 'hdrh.Rango_de_horario', '=', 'r.idRangoDeHorario')
            ->select(
                'h.idHorario',
                'a.nombre as asignatura',
                'g.claveGrupo',
                'g.semestre',
                'm.nombreModalidad as modalidad',
                'b.numeroBloque',
                'au.nombreAula as aula',
                'ds.nombreDia as dia',
                'r.horaInicio',
                'r.horaFin',
                DB::raw("TRIM(CONCAT_WS(' ', u.primerNombre, u.segundoNombre, u.primerApellido, u.segundoApellido)) as docente")
            )
            ->where('g.idEstatus', 1);

        if (!empty($buscar)) {
            $query->where(function ($q) use ($buscar) {
                $q->where('a.nombre', 'like', "%{$buscar}%")
                    ->orWhere('g.claveGrupo', 'like', "%{$buscar}%")
                    ->orWhere('au.nombreAula', 'like', "%{$buscar}%")
                    ->orWhere('ds.nombreDia', 'like', "%{$buscar}%")
                    ->orWhereRaw(
                        "CONCAT_WS(' ', u.primerNombre, u.segundoNombre, u.primerApellido, u.segundoApellido) LIKE ?",
                        ["%{$buscar}%"]
                    );
            });
        }

        if (!empty($dia)) {
            $query->where('ds.idDiaSemana', (int) $dia);
        }

        switch ($orden) {
            case 'asignatura_az':
                $query->orderBy('a.nombre');
                break;
            case 'asignatura_za':
                $query->orderByDesc('a.nombre');
                break;
            case 'grupo_az':
                $query->orderBy('g.claveGrupo');
                break;
            case 'grupo_za':
                $query->orderByDesc('g.claveGrupo');
                break;
            case 'docente_az':
                $query->orderBy('u.primerApellido')->orderBy('u.primerNombre');
                break;
            case 'docente_za':
                $query->orderByDesc('u.primerApellido')->orderByDesc('u.primerNombre');
                break;
            case 'aula_az':
                $query->orderBy('au.nombreAula');
                break;
            case 'aula_za':
                $query->orderByDesc('au.nombreAula');
                break;
            case 'hora_asc':
                $query->orderBy('r.horaInicio');
                break;
            default:
                $query->orderBy('a.nombre');
                break;
        }

        $horarios = $query->paginate(10)->withQueryString();
        $dias = DB::table('Dia_semana')->orderBy('idDiaSemana')->get();

        return view('SGAIDMA.moduloCalificaciones.consultaDeCalificaciones', compact(
            'horarios',
            'buscar',
            'orden',
            'dia',
            'dias'
        ));
    }

    public function edit($id)
    {
        $this->validarAcceso();

        $horario = DB::table('Horario as h')
            ->join('Asignatura as a', 'h.idAsignatura', '=', 'a.idAsignatura')
            ->join('Grupo as g', 'h.idGrupo', '=', 'g.idGrupo')
            ->select('h.idHorario', 'h.idGrupo', 'a.nombre as asignatura', 'g.claveGrupo')
            ->where('h.idHorario', $id)
            ->first();

        if (!$horario) {
            abort(404);
        }

        $estudiantes = DB::table('Grupo_estudiante as ge')
            ->join('Estudiante as e', 'ge.idEstudiante', '=', 'e.idEstudiante')
            ->join('Usuario as u', 'e.idUsuario', '=', 'u.idUsuario')
            ->where('ge.idGrupo', $horario->idGrupo)
            ->where('u.idestatus', 1)
            ->select(
                'e.idEstudiante',
                'u.idUsuario',
                DB::raw("TRIM(CONCAT_WS(' ', u.primerNombre, u.segundoNombre, u.primerApellido, u.segundoApellido)) as nombre")
            )
            ->orderBy('u.primerApellido')
            ->orderBy('u.primerNombre')
            ->get();

        return view('SGAIDMA.moduloCalificaciones.editarDeCalificaciones', compact('horario', 'estudiantes'));
    }
}
