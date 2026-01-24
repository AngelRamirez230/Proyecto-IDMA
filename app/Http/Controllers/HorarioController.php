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

            $traslapeGrupo = $this->existeTraslape($dia, $inicio, $fin, $idBloque, $idGrupo, null, null);
            if ($traslapeGrupo) {
                return 'El grupo ya tiene una asignatura en ese dia y rango de horas.';
            }

            if (!empty($entry['idDocente'])) {
                $traslapeDocente = $this->existeTraslape($dia, $inicio, $fin, $idBloque, null, $entry['idDocente'], null);
                if ($traslapeDocente) {
                    return 'El docente seleccionado ya tiene un horario en ese dia y rango.';
                }
            }

            if (!empty($entry['idAula'])) {
                $traslapeAula = $this->existeTraslape($dia, $inicio, $fin, $idBloque, null, null, $entry['idAula']);
                if ($traslapeAula) {
                    return 'El aula seleccionada ya tiene un horario en ese dia y rango.';
                }
            }
        }

        return null;
    }

    private function existeTraslape($dia, $inicio, $fin, $idBloque, $idGrupo = null, $idDocente = null, $idAula = null): bool
    {
        $query = DB::table('Horario as h')
            ->join('horario_dia_rango_horario as hdrh', 'h.idHorario', '=', 'hdrh.Horario_idHorario')
            ->join('Rango_de_horario as r', 'hdrh.Rango_de_horario', '=', 'r.idRangoDeHorario')
            ->where('hdrh.Dia_semana_idDiaSemana', $dia)
            ->where('h.idBloque', $idBloque)
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('r.horaInicio', '<', $fin)
                    ->where('r.horaFin', '>', $inicio);
            });

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
}
