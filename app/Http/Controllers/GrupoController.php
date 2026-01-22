<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\CicloEscolar;
use App\Models\Generacion;
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

    private function validarAccesoAlta()
    {
        $usuario = Auth::user();

        if (!$usuario || (int) $usuario->idtipoDeUsuario !== 1) {
            abort(403, 'No autorizado');
        }
    }

    public function create()
    {
        $this->validarAccesoAlta();

        $licenciaturas = Licenciatura::orderBy('nombreLicenciatura')->get();
        $ciclos = CicloEscolar::orderBy('idCicloEscolar', 'desc')->get();
        $generaciones = Generacion::orderBy('idGeneracion', 'desc')->get();

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
            compact('licenciaturas', 'ciclos', 'cicloModalidades', 'generaciones')
        );
    }

    public function store(Request $request)
    {
        $this->validarAccesoAlta();

        $request->validate([
            'idCicloEscolar' => 'required|exists:Ciclo_escolar,idCicloEscolar',
            'idCicloModalidad' => 'required|exists:Ciclo_modalidad,idCicloModalidad',
            'idLicenciatura' => 'required|exists:Licenciatura,idLicenciatura',
            'semestre' => 'required|integer|min:1|max:12',
            'claveGrupo' => 'nullable|string|max:45',
            'idGeneracion' => 'nullable|exists:Generacion,idGeneracion',
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
        $idGeneracion = $request->filled('idGeneracion') ? (int) $request->idGeneracion : null;

        if (!$idGeneracion && !$claveManual) {
            return back()
                ->with('popupError', 'Debes seleccionar una generacion o ingresar una clave de grupo manualmente.')
                ->withInput();
        }

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

            $claveGeneracion = null;
            if ($idGeneracion) {
                $claveGeneracion = DB::table('Generacion')
                    ->where('idGeneracion', $idGeneracion)
                    ->value('claveGeneracion');
            }

            $claveGrupo = $claveManual ?: $this->generarClaveGrupo(
                $request->semestre,
                $abreviacion,
                $claveGeneracion
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
                'idGeneracion' => $idGeneracion,
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

    private function generarClaveGrupo($semestre, $abreviacionLicenciatura = null, $claveGeneracion = null)
    {
        $semestreDos = str_pad((string) $semestre, 2, '0', STR_PAD_LEFT);

        $prefijo = ($abreviacionLicenciatura ?: '') . $semestreDos . ($claveGeneracion ?: '');

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

        if ((int) Auth::user()->idtipoDeUsuario !== 1) {
            $filtro = 'activos';
        }

        $query = DB::table('Grupo as g')
            ->join('Licenciatura as l', 'g.idLicenciatura', '=', 'l.idLicenciatura')
            ->join('Ciclo_modalidad as cm', 'g.idCicloModalidad', '=', 'cm.idCicloModalidad')
            ->join('Modalidad as m', 'cm.idModalidad', '=', 'm.idModalidad')
            ->join('Ciclo_escolar as ce', 'cm.idCicloEscolar', '=', 'ce.idCicloEscolar')
            ->leftJoin('Generacion as gen', 'g.idGeneracion', '=', 'gen.idGeneracion')
            ->leftJoin(
                DB::raw('(select idGrupo, count(*) as inscritos from Grupo_estudiante group by idGrupo) ge'),
                'g.idGrupo',
                '=',
                'ge.idGrupo'
            )
            ->select(
                'g.idGrupo',
                'g.claveGrupo',
                'g.semestre',
                'g.idEstatus',
                'l.nombreLicenciatura',
                'm.nombreModalidad',
                'ce.nombreCicloEscolar',
                'ce.idCicloEscolar',
                'gen.claveGeneracion',
                DB::raw('COALESCE(ge.inscritos, 0) as inscritos')
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
            ->leftJoin('Generacion as gen', 'g.idGeneracion', '=', 'gen.idGeneracion')
            ->select(
                'g.idGrupo',
                'g.claveGrupo',
                'g.semestre',
                'g.idEstatus',
                'l.nombreLicenciatura',
                'm.nombreModalidad',
                'ce.nombreCicloEscolar',
                'gen.claveGeneracion'
            )
            ->where('g.idGrupo', $id)
            ->first();

        if (!$grupo) {
            abort(404);
        }

        $estudiantes = DB::table('Grupo_estudiante as ge')
            ->join('Estudiante as e', 'ge.idEstudiante', '=', 'e.idEstudiante')
            ->join('Usuario as u', 'e.idUsuario', '=', 'u.idUsuario')
            ->leftJoin('Generacion as g', 'e.idGeneracion', '=', 'g.idGeneracion')
            ->select(
                'e.idEstudiante',
                'e.matriculaAlfanumerica',
                'g.claveGeneracion',
                'u.primerNombre',
                'u.segundoNombre',
                'u.primerApellido',
                'u.segundoApellido'
            )
            ->where('ge.idGrupo', $id)
            ->orderBy('u.primerApellido')
            ->orderBy('u.primerNombre')
            ->get();
        $inscritos = $estudiantes->count();

        return view(
            'SGAIDMA.moduloGrupos.detalleDeGrupo',
            compact('grupo', 'estudiantes', 'inscritos')
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
                'g.idGeneracion',
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

        $generaciones = Generacion::orderBy('idGeneracion', 'desc')->get();

        $estudiantes = DB::table('Grupo_estudiante as ge')
            ->join('Estudiante as e', 'ge.idEstudiante', '=', 'e.idEstudiante')
            ->join('Usuario as u', 'e.idUsuario', '=', 'u.idUsuario')
            ->leftJoin('Generacion as g', 'e.idGeneracion', '=', 'g.idGeneracion')
            ->select(
                'e.idEstudiante',
                'e.matriculaAlfanumerica',
                'g.claveGeneracion',
                'u.primerNombre',
                'u.segundoNombre',
                'u.primerApellido',
                'u.segundoApellido'
            )
            ->where('ge.idGrupo', $id)
            ->orderBy('u.primerApellido')
            ->orderBy('u.primerNombre')
            ->get();

        $disponibles = DB::table('Estudiante as e')
            ->join('Usuario as u', 'e.idUsuario', '=', 'u.idUsuario')
            ->leftJoin('Grupo_estudiante as ge', 'e.idEstudiante', '=', 'ge.idEstudiante')
            ->leftJoin('Generacion as g', 'e.idGeneracion', '=', 'g.idGeneracion')
            ->select(
                'e.idEstudiante',
                'e.matriculaAlfanumerica',
                'g.claveGeneracion',
                'e.grado',
                'u.primerNombre',
                'u.segundoNombre',
                'u.primerApellido',
                'u.segundoApellido'
            )
            ->where('e.idEstatus', 4)
            ->where('u.idestatus', 1)
            ->whereNull('ge.idGrupo')
            ->orderBy('u.primerApellido')
            ->orderBy('u.primerNombre')
            ->get();

        return view(
            'SGAIDMA.moduloGrupos.editarDeGrupo',
            compact('grupo', 'licenciaturas', 'ciclos', 'cicloModalidades', 'generaciones', 'estudiantes', 'disponibles')
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
            'idGeneracion' => 'nullable|exists:Generacion,idGeneracion',
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
        $idGeneracion = $request->filled('idGeneracion') ? (int) $request->idGeneracion : null;

        if (!$idGeneracion && !$claveManual) {
            return back()
                ->with('popupError', 'Debes seleccionar una generacion o ingresar una clave de grupo manualmente.')
                ->withInput();
        }

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

        $claveGeneracion = null;
        if ($idGeneracion) {
            $claveGeneracion = DB::table('Generacion')
                ->where('idGeneracion', $idGeneracion)
                ->value('claveGeneracion');
        }

        $claveGrupo = $claveManual ?: $this->generarClaveGrupo(
            $request->semestre,
            $abreviacion,
            $claveGeneracion
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
            'idGeneracion' => $idGeneracion,
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

    public function asignarEstudiantes(Request $request, $id)
    {
        $this->validarAcceso();

        if ((int) Auth::user()->idtipoDeUsuario !== 1) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'estudiantes' => 'required|array',
            'estudiantes.*' => 'integer',
        ]);

        $grupo = Grupo::findOrFail($id);
        $ids = $request->estudiantes;

        $validos = DB::table('Estudiante as e')
            ->join('Usuario as u', 'e.idUsuario', '=', 'u.idUsuario')
            ->leftJoin('Grupo_estudiante as ge', 'e.idEstudiante', '=', 'ge.idEstudiante')
            ->where('e.idEstatus', 4)
            ->where('u.idestatus', 1)
            ->whereNull('ge.idGrupo')
            ->whereIn('e.idEstudiante', $ids)
            ->pluck('e.idEstudiante')
            ->toArray();

        if (empty($validos)) {
            return back()
                ->with('popupError', 'No hay estudiantes disponibles para asignar.')
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $payload = array_map(function ($idEstudiante) use ($grupo) {
                return [
                    'idGrupo' => $grupo->idGrupo,
                    'idEstudiante' => $idEstudiante,
                ];
            }, $validos);

            DB::table('Grupo_estudiante')->insert($payload);

            DB::commit();

            return redirect()
                ->route('grupos.edit', $grupo->idGrupo)
                ->with('success', 'Estudiantes asignados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function desasignarEstudiantes(Request $request, $id)
    {
        $this->validarAcceso();

        if ((int) Auth::user()->idtipoDeUsuario !== 1) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'estudiantes' => 'required|array',
            'estudiantes.*' => 'integer',
        ]);

        $ids = $request->estudiantes;

        DB::table('Grupo_estudiante')
            ->where('idGrupo', $id)
            ->whereIn('idEstudiante', $ids)
            ->delete();

        return redirect()
            ->route('grupos.edit', $id)
            ->with('success', 'Estudiantes desasignados correctamente.');
    }
    public function toggleEstatus($id)
    {
        $this->validarAcceso();

        if ((int) Auth::user()->idtipoDeUsuario !== 1) {
            abort(403, 'No autorizado');
        }

        $grupo = Grupo::findOrFail($id);
        $nuevo = ((int) $grupo->idEstatus === 2) ? 1 : 2;

        $grupo->update([
            'idEstatus' => $nuevo,
        ]);

        return redirect()
            ->route('consultaGrupo')
            ->with('success', 'Estatus actualizado correctamente.');
    }
}
