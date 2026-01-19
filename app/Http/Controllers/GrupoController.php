<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\CicloEscolar;
use App\Models\Grupo;
use App\Models\Licenciatura;
use App\Models\CicloModalidad;

class GrupoController extends Controller
{
    private function validarAcceso()
    {
        $usuario = Auth::user();

        if (!$usuario || !in_array((int) $usuario->idtipoDeUsuario, [1, 2], true)) {
            abort(403, 'No autorizado');
        }
    }

    public function create()
    {
        $this->validarAcceso();

        $licenciaturas = Licenciatura::orderBy('nombreLicenciatura')->get();
        $ciclos = CicloEscolar::orderBy('idCicloEscolar', 'desc')->get();

        $cicloModalidades = DB::table('Ciclo_modalidad as cm')
            ->join('Modalidad as m', 'cm.idModalidad', '=', 'm.idModalidad')
            ->select(
                'cm.idCicloModalidad',
                'cm.idCicloEscolar',
                'cm.idModalidad',
                'm.nombreModalidad',
                'cm.fechaInicio'
            )
            ->orderBy('cm.idCicloEscolar')
            ->orderBy('m.nombreModalidad')
            ->get();

        return view(
            'SGAIDMA.moduloGrupos.altaDeGrupo',
            compact('licenciaturas', 'ciclos', 'cicloModalidades')
        );
    }

    public function store(Request $request)
    {
        $this->validarAcceso();

        $request->validate([
            'idCicloEscolar' => 'required|exists:Ciclo_escolar,idCicloEscolar',
            'idCicloModalidad' => 'required|exists:Ciclo_modalidad,idCicloModalidad',
            'idLicenciatura' => 'required|exists:Licenciatura,idLicenciatura',
            'semestre' => 'required|integer|min:1|max:12',
            'claveGrupo' => 'nullable|string|max:45',
        ]);

        $cicloModalidad = DB::table('Ciclo_modalidad')
            ->where('idCicloModalidad', $request->idCicloModalidad)
            ->first();

        if (!$cicloModalidad || (int) $cicloModalidad->idCicloEscolar !== (int) $request->idCicloEscolar) {
            return back()
                ->with('popupError', 'La modalidad seleccionada no pertenece al ciclo escolar elegido.')
                ->withInput();
        }

        $claveManual = $request->filled('claveGrupo') ? trim($request->claveGrupo) : null;

        if ($claveManual) {
            $existeClave = Grupo::where('claveGrupo', $claveManual)->exists();
            if ($existeClave) {
                return back()
                    ->with('popupError', 'La clave del grupo ya existe.')
                    ->withInput();
            }
        }

        $existeCombinacion = Grupo::where('idCicloModalidad', $request->idCicloModalidad)
            ->where('idLicenciatura', $request->idLicenciatura)
            ->where('semestre', $request->semestre)
            ->exists();

        if ($existeCombinacion) {
            return back()
                ->with('popupError', 'Ya existe un grupo registrado con ese ciclo y semestre.')
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $licenciatura = Licenciatura::find($request->idLicenciatura);
            $abreviacion = $licenciatura?->abreviacionLicenciatura;

            $claveGrupo = $claveManual ?: $this->generarClaveGrupo(
                $request->semestre,
                $cicloModalidad->fechaInicio,
                $abreviacion
            );

            if (!$claveManual) {
                $existeClave = Grupo::where('claveGrupo', $claveGrupo)->exists();
                if ($existeClave) {
                    return back()
                        ->with('popupError', 'Ya existe un grupo con la clave generada. Intenta de nuevo.')
                        ->withInput();
                }
            }

            Grupo::create([
                'claveGrupo' => $claveGrupo,
                'semestre' => $request->semestre,
                'idLicenciatura' => $request->idLicenciatura,
                'idCicloModalidad' => $request->idCicloModalidad,
                'idEstatus' => 1,
            ]);

            DB::commit();

            return redirect()
                ->route('altaGrupo')
                ->with('success', 'Alta de grupo realizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function generarClaveGrupo($semestre, $fechaInicio, $abreviacionLicenciatura = null)
    {
        $semestreDos = str_pad((string) $semestre, 2, '0', STR_PAD_LEFT);

        $fecha = date_create($fechaInicio);
        $anioDos = $fecha ? $fecha->format('y') : date('y');
        $mes = $fecha ? (int) $fecha->format('m') : (int) date('m');

        $bloque = $mes <= 6 ? 'A' : 'B';
        $prefijo = ($abreviacionLicenciatura ?: '') . $semestreDos . $anioDos . $bloque;

        $maxConsecutivo = DB::table('Grupo')
            ->where('claveGrupo', 'like', $prefijo . '__')
            ->selectRaw('MAX(CAST(RIGHT(claveGrupo, 2) AS UNSIGNED)) as maximo')
            ->value('maximo');

        $siguiente = (int) $maxConsecutivo + 1;
        $consecutivo = str_pad((string) $siguiente, 2, '0', STR_PAD_LEFT);

        return $prefijo . $consecutivo;
    }

    public function index(Request $request)
    {
        $this->validarAcceso();

        $buscar = $request->buscarGrupo;
        $orden = $request->orden;
        $filtro = $request->filtro ?: 'activos';

        $query = DB::table('Grupo as g')
            ->join('Licenciatura as l', 'g.idLicenciatura', '=', 'l.idLicenciatura')
            ->join('Ciclo_modalidad as cm', 'g.idCicloModalidad', '=', 'cm.idCicloModalidad')
            ->join('Modalidad as m', 'cm.idModalidad', '=', 'm.idModalidad')
            ->join('Ciclo_escolar as ce', 'cm.idCicloEscolar', '=', 'ce.idCicloEscolar')
            ->select(
                'g.idGrupo',
                'g.claveGrupo',
                'g.semestre',
                'g.idEstatus',
                'l.nombreLicenciatura',
                'm.nombreModalidad',
                'ce.nombreCicloEscolar',
                'ce.idCicloEscolar'
            );

        if ($request->filled('buscarGrupo')) {
            $query->where(function ($q) use ($buscar) {
                $q->where('g.claveGrupo', 'like', "%{$buscar}%")
                    ->orWhere('l.nombreLicenciatura', 'like', "%{$buscar}%")
                    ->orWhere('ce.nombreCicloEscolar', 'like', "%{$buscar}%")
                    ->orWhere('g.semestre', 'like', "%{$buscar}%");
            });
        }

        if ($filtro === 'activos') {
            $query->where('g.idEstatus', 1);
        } elseif ($filtro === 'suspendidos') {
            $query->where('g.idEstatus', 2);
        }

        $ordenes = [
            'clave' => ['g.claveGrupo', 'asc'],
            'semestre' => ['g.semestre', 'asc'],
            'periodo_antiguo' => ['ce.idCicloEscolar', 'asc'],
            'periodo_reciente' => ['ce.idCicloEscolar', 'desc'],
        ];

        if (isset($ordenes[$orden])) {
            $query->orderBy($ordenes[$orden][0], $ordenes[$orden][1]);
        } else {
            $query->orderBy('g.claveGrupo');
        }

        $grupos = $query->paginate(10)->withQueryString();

        return view(
            'SGAIDMA.moduloGrupos.consultaDeGrupo',
            compact('grupos', 'buscar', 'orden', 'filtro')
        );
    }

    public function show($id)
    {
        $this->validarAcceso();

        $grupo = DB::table('Grupo as g')
            ->join('Licenciatura as l', 'g.idLicenciatura', '=', 'l.idLicenciatura')
            ->join('Ciclo_modalidad as cm', 'g.idCicloModalidad', '=', 'cm.idCicloModalidad')
            ->join('Modalidad as m', 'cm.idModalidad', '=', 'm.idModalidad')
            ->join('Ciclo_escolar as ce', 'cm.idCicloEscolar', '=', 'ce.idCicloEscolar')
            ->select(
                'g.idGrupo',
                'g.claveGrupo',
                'g.semestre',
                'g.idEstatus',
                'l.nombreLicenciatura',
                'm.nombreModalidad',
                'ce.nombreCicloEscolar'
            )
            ->where('g.idGrupo', $id)
            ->first();

        if (!$grupo) {
            abort(404);
        }

        $estudiantes = DB::table('Grupo_estudiante as ge')
            ->join('Estudiante as e', 'ge.idEstudiante', '=', 'e.idEstudiante')
            ->join('Usuario as u', 'e.idUsuario', '=', 'u.idUsuario')
            ->select(
                'e.matriculaAlfanumerica',
                'e.idGeneracion',
                'u.primerNombre',
                'u.segundoNombre',
                'u.primerApellido',
                'u.segundoApellido'
            )
            ->where('ge.idGrupo', $id)
            ->orderBy('u.primerApellido')
            ->orderBy('u.primerNombre')
            ->get();

        return view(
            'SGAIDMA.moduloGrupos.detalleDeGrupo',
            compact('grupo', 'estudiantes')
        );
    }

    public function edit($id)
    {
        $this->validarAcceso();

        if ((int) Auth::user()->idtipoDeUsuario !== 1) {
            abort(403, 'No autorizado');
        }

        $grupo = DB::table('Grupo as g')
            ->join('Ciclo_modalidad as cm', 'g.idCicloModalidad', '=', 'cm.idCicloModalidad')
            ->select(
                'g.idGrupo',
                'g.claveGrupo',
                'g.semestre',
                'g.idLicenciatura',
                'g.idCicloModalidad',
                'cm.idCicloEscolar'
            )
            ->where('g.idGrupo', $id)
            ->first();

        if (!$grupo) {
            abort(404);
        }

        $licenciaturas = Licenciatura::orderBy('nombreLicenciatura')->get();
        $ciclos = CicloEscolar::orderBy('idCicloEscolar', 'desc')->get();

        $cicloModalidades = DB::table('Ciclo_modalidad as cm')
            ->join('Modalidad as m', 'cm.idModalidad', '=', 'm.idModalidad')
            ->select(
                'cm.idCicloModalidad',
                'cm.idCicloEscolar',
                'cm.idModalidad',
                'm.nombreModalidad',
                'cm.fechaInicio'
            )
            ->orderBy('cm.idCicloEscolar')
            ->orderBy('m.nombreModalidad')
            ->get();

        return view(
            'SGAIDMA.moduloGrupos.editarDeGrupo',
            compact('grupo', 'licenciaturas', 'ciclos', 'cicloModalidades')
        );
    }

    public function update(Request $request, $id)
    {
        $this->validarAcceso();

        if ((int) Auth::user()->idtipoDeUsuario !== 1) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'idCicloEscolar' => 'required|exists:Ciclo_escolar,idCicloEscolar',
            'idCicloModalidad' => 'required|exists:Ciclo_modalidad,idCicloModalidad',
            'idLicenciatura' => 'required|exists:Licenciatura,idLicenciatura',
            'semestre' => 'required|integer|min:1|max:12',
            'claveGrupo' => 'nullable|string|max:45',
        ]);

        $grupo = Grupo::findOrFail($id);

        $cicloModalidad = DB::table('Ciclo_modalidad')
            ->where('idCicloModalidad', $request->idCicloModalidad)
            ->first();

        if (!$cicloModalidad || (int) $cicloModalidad->idCicloEscolar !== (int) $request->idCicloEscolar) {
            return back()
                ->with('popupError', 'La modalidad seleccionada no pertenece al ciclo escolar elegido.')
                ->withInput();
        }

        $claveManual = $request->filled('claveGrupo') ? trim($request->claveGrupo) : null;

        if ($claveManual) {
            $existeClave = Grupo::where('claveGrupo', $claveManual)
                ->where('idGrupo', '!=', $id)
                ->exists();
            if ($existeClave) {
                return back()
                    ->with('popupError', 'La clave del grupo ya existe.')
                    ->withInput();
            }
        }

        $existeCombinacion = Grupo::where('idCicloModalidad', $request->idCicloModalidad)
            ->where('idLicenciatura', $request->idLicenciatura)
            ->where('semestre', $request->semestre)
            ->where('idGrupo', '!=', $id)
            ->exists();

        if ($existeCombinacion) {
            return back()
                ->with('popupError', 'Ya existe un grupo registrado con ese ciclo y semestre.')
                ->withInput();
        }

        $licenciatura = Licenciatura::find($request->idLicenciatura);
        $abreviacion = $licenciatura?->abreviacionLicenciatura;

        $claveGrupo = $claveManual ?: $this->generarClaveGrupo(
            $request->semestre,
            $cicloModalidad->fechaInicio,
            $abreviacion
        );

        if (!$claveManual) {
            $existeClave = Grupo::where('claveGrupo', $claveGrupo)
                ->where('idGrupo', '!=', $id)
                ->exists();
            if ($existeClave) {
                return back()
                    ->with('popupError', 'Ya existe un grupo con la clave generada. Intenta de nuevo.')
                    ->withInput();
            }
        }

        $grupo->update([
            'claveGrupo' => $claveGrupo,
            'semestre' => $request->semestre,
            'idLicenciatura' => $request->idLicenciatura,
            'idCicloModalidad' => $request->idCicloModalidad,
        ]);

        return redirect()
            ->route('grupos.show', $grupo->idGrupo)
            ->with('success', 'Grupo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $this->validarAcceso();

        if ((int) Auth::user()->idtipoDeUsuario !== 1) {
            abort(403, 'No autorizado');
        }

        DB::beginTransaction();

        try {
            DB::table('Grupo_estudiante')
                ->where('idGrupo', $id)
                ->delete();

            Grupo::where('idGrupo', $id)->delete();

            DB::commit();

            return redirect()
                ->route('consultaGrupo')
                ->with('success', 'Grupo eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
