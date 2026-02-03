<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Asignatura;
use App\Models\Aula;
use App\Models\Bloque;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\DiaSemana;
use App\Models\RangoDeHorario;

class HorarioController extends Controller
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

    public function apartado()
    {
        $this->validarAcceso();

        return view('SGAIDMA.moduloHorarios.apartadoHorarios');
    }

    public function create()
    {
        $this->validarAcceso();

        $grupos = Grupo::where('idEstatus', 1)
            ->orderBy('claveGrupo')
            ->get();

        $ciclos = CicloEscolar::orderBy('idCicloEscolar', 'desc')->get();

        $bloques = DB::table('Bloque as b')
            ->join('Ciclo_modalidad as cm', 'b.idCicloModalidad', '=', 'cm.idCicloModalidad')
            ->join('Modalidad as m', 'cm.idModalidad', '=', 'm.idModalidad')
            ->select(
                'b.idBloque',
                'b.numeroBloque',
                'b.idCicloModalidad',
                'cm.idCicloEscolar',
                'm.nombreModalidad'
            )
            ->where('b.idTipoDeEstatus', 1)
            ->orderBy('b.numeroBloque')
            ->get();

        $asignaturas = Asignatura::orderBy('nombre')->get();

        $docentes = Docente::with('usuario')
            ->get()
            ->map(function ($docente) {
                $u = $docente->usuario;
                $nombre = trim(
                    ($u->primerNombre ?? '') . ' ' .
                    ($u->segundoNombre ?? '') . ' ' .
                    ($u->primerApellido ?? '') . ' ' .
                    ($u->segundoApellido ?? '')
                );
                return (object) [
                    'idDocente' => $docente->idDocente,
                    'nombre' => $nombre ?: ('Docente ' . $docente->idDocente),
                ];
            });

        $aulas = Aula::orderBy('nombreAula')->get();
        $dias = DiaSemana::orderBy('idDiaSemana')->get();

        return view(
            'SGAIDMA.moduloHorarios.altaDeHorario',
            compact('grupos', 'ciclos', 'bloques', 'asignaturas', 'docentes', 'aulas', 'dias')
        );
    }

    public function store(Request $request)
    {
        $this->validarAcceso();

        $request->validate([
            'idGrupo' => 'required|exists:Grupo,idGrupo',
            'idCicloEscolar' => 'required|exists:Ciclo_escolar,idCicloEscolar',
            'idBloque' => 'required|exists:Bloque,idBloque',
            'horarios' => 'required|array|min:1',
            'horarios.*' => 'string',
        ], [
            'idGrupo.required' => 'El grupo es obligatorio.',
            'idGrupo.exists' => 'El grupo seleccionado no es válido.',
            'idCicloEscolar.required' => 'El ciclo escolar es obligatorio.',
            'idCicloEscolar.exists' => 'El ciclo escolar seleccionado no es válido.',
            'idBloque.required' => 'El bloque es obligatorio.',
            'idBloque.exists' => 'El bloque seleccionado no es válido.',
            'horarios.required' => 'El campo horarios es obligatorio.',
            'horarios.array' => 'El campo horarios debe ser un arreglo.',
            'horarios.min' => 'Debe capturar al menos un horario.',
            'horarios.*.string' => 'Cada horario debe enviarse como texto.',
        ]);

        $bloque = DB::table('Bloque as b')
            ->join('Ciclo_modalidad as cm', 'b.idCicloModalidad', '=', 'cm.idCicloModalidad')
            ->select('b.idBloque', 'cm.idCicloEscolar')
            ->where('b.idBloque', $request->idBloque)
            ->first();

        if (!$bloque || (int) $bloque->idCicloEscolar !== (int) $request->idCicloEscolar) {
            return back()
                ->with('popupError', 'El bloque seleccionado no pertenece al ciclo escolar.')
                ->withInput();
        }

        $entries = [];
        foreach ($request->horarios as $raw) {
            $data = json_decode($raw, true);
            if (!$data) {
                continue;
            }
            $entries[] = $data;
        }

        if (empty($entries)) {
            return back()
                ->with('popupError', 'No hay horarios válidos para guardar.')
                ->withInput();
        }

        $asignaturasRepetidas = $this->validarAsignaturaRepetida($entries, (int) $request->idGrupo);
        if ($asignaturasRepetidas) {
            return back()
                ->with('popupError', $asignaturasRepetidas)
                ->withInput();
        }

        $errores = $this->validarTraslapes($entries, (int) $request->idGrupo, (int) $request->idBloque);
        if ($errores) {
            return back()
                ->with('popupError', $errores)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            foreach ($entries as $entry) {
                $rango = $this->obtenerORCrearRango($entry['hora_inicio'], $entry['hora_fin']);

                $horario = Horario::create([
                    'idAsignatura' => $entry['idAsignatura'],
                    'idGrupo' => $request->idGrupo,
                    'idDocente' => $entry['idDocente'] ?: null,
                    'idAula' => $entry['idAula'] ?: null,
                    'idBloque' => $request->idBloque,
                ]);

                DB::table('horario_dia_rango_horario')->insert([
                    'Horario_idHorario' => $horario->idHorario,
                    'Dia_semana_idDiaSemana' => $entry['idDiaSemana'],
                    'Rango_de_horario' => $rango->idRangoDeHorario,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('altaHorario')
                ->with('success', 'Horario registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function validarTraslapes(array $entries, int $idGrupo, int $idBloque): ?string
    {
        $porDia = [];
        foreach ($entries as $entry) {
            if (empty($entry['idDiaSemana']) || empty($entry['idAsignatura'])) {
                return 'Cada horario debe tener dia y asignatura.';
            }
            if (empty($entry['hora_inicio']) || empty($entry['hora_fin'])) {
                return 'Cada horario debe tener hora de inicio y fin.';
            }
            if ($entry['hora_inicio'] >= $entry['hora_fin']) {
                return 'La hora de inicio debe ser menor a la hora de fin.';
            }
            $porDia[$entry['idDiaSemana']][] = $entry;
        }

        foreach ($porDia as $dia => $items) {
            $count = count($items);
            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($this->seTraslapan($items[$i]['hora_inicio'], $items[$i]['hora_fin'], $items[$j]['hora_inicio'], $items[$j]['hora_fin'])) {
                        return 'Hay traslapes entre horarios capturados para el mismo dia.';
                    }
                }
            }
        }

        foreach ($entries as $entry) {
            $dia = $entry['idDiaSemana'];
            $inicio = $entry['hora_inicio'];
            $fin = $entry['hora_fin'];

            $traslapeGrupo = $this->existeTraslape($dia, $inicio, $fin, null, $idGrupo, null, null);
            if ($traslapeGrupo) {
                return 'El grupo ya tiene una asignatura en ese dia y rango de horas.';
            }

            if (!empty($entry['idDocente'])) {
                $traslapeDocente = $this->existeTraslape($dia, $inicio, $fin, null, null, $entry['idDocente'], null);
                if ($traslapeDocente) {
                    return 'El docente seleccionado ya tiene un horario en ese dia y rango.';
                }
            }

            if (!empty($entry['idAula'])) {
                $traslapeAula = $this->existeTraslape($dia, $inicio, $fin, null, null, null, $entry['idAula']);
                if ($traslapeAula) {
                    return 'El aula seleccionada ya tiene un horario en ese dia y rango.';
                }
            }
        }

        return null;
    }

    private function validarAsignaturaRepetida(array $entries, int $idGrupo): ?string
    {
        $asignaturas = [];
        foreach ($entries as $entry) {
            if (!empty($entry['idAsignatura'])) {
                $asignaturas[] = (int) $entry['idAsignatura'];
            }
        }

        if (count($asignaturas) !== count(array_unique($asignaturas))) {
            return 'La asignatura no puede repetirse en el mismo grupo.';
        }

        $asignaturas = array_values(array_unique($asignaturas));
        if (empty($asignaturas)) {
            return null;
        }

        $existe = Horario::where('idGrupo', $idGrupo)
            ->whereIn('idAsignatura', $asignaturas)
            ->exists();

        return $existe ? 'La asignatura no puede repetirse en el mismo grupo.' : null;
    }

    private function existeTraslape($dia, $inicio, $fin, $idBloque = null, $idGrupo = null, $idDocente = null, $idAula = null): bool
    {
        $query = DB::table('Horario as h')
            ->join('horario_dia_rango_horario as hdrh', 'h.idHorario', '=', 'hdrh.Horario_idHorario')
            ->join('Rango_de_horario as r', 'hdrh.Rango_de_horario', '=', 'r.idRangoDeHorario')
            ->where('hdrh.Dia_semana_idDiaSemana', $dia)
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('r.horaInicio', '<', $fin)
                    ->where('r.horaFin', '>', $inicio);
            });

        if ($idBloque) {
            $query->where('h.idBloque', $idBloque);
        }

        if ($idGrupo) {
            $query->where('h.idGrupo', $idGrupo);
        }

        if ($idDocente) {
            $query->where('h.idDocente', $idDocente);
        }

        if ($idAula) {
            $query->where('h.idAula', $idAula);
        }

        return $query->exists();
    }

    private function seTraslapan($inicioA, $finA, $inicioB, $finB): bool
    {
        return $inicioA < $finB && $finA > $inicioB;
    }

    private function obtenerORCrearRango($inicio, $fin)
    {
        $rango = RangoDeHorario::where('horaInicio', $inicio)
            ->where('horaFin', $fin)
            ->where('idTipoDeEstatus', 1)
            ->first();

        if ($rango) {
            return $rango;
        }

        return RangoDeHorario::create([
            'horaInicio' => $inicio,
            'horaFin' => $fin,
            'idTipoDeEstatus' => 1,
        ]);
    }

    public function destroy($id)
    {
        $this->validarAcceso();

        DB::beginTransaction();

        try {
            DB::table('horario_dia_rango_horario')
                ->where('Horario_idHorario', $id)
                ->delete();

            Horario::where('idHorario', $id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()
            ->route('consultaHorarios')
            ->with('success', 'Horario eliminado correctamente.');
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
        $dias = DiaSemana::orderBy('idDiaSemana')->get();

        return view('SGAIDMA.moduloHorarios.consultaDeHorarios', compact(
            'horarios',
            'buscar',
            'orden',
            'dia',
            'dias'
        ));
    }
}


