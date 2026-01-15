<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Asignatura;
use App\Models\Empleado;
use App\Models\NivelDeFormacion;
use App\Models\PlanDeEstudios;

class AsignaturaController extends Controller
{
    private function usuarioAutorizado($usuario, array $departamentosAutorizados): bool
    {
        if (!$usuario) {
            return false;
        }

        if ((int) $usuario->idtipoDeUsuario === 1) {
            return true;
        }

        $empleado = Empleado::where('idUsuario', $usuario->idUsuario)->first();

        if (!$empleado) {
            return false;
        }

        return in_array((int) $empleado->idDepartamento, $departamentosAutorizados, true);
    }

    private function validarAccesoAlta()
    {
        $usuario = Auth::user();

        if (!$this->usuarioAutorizado($usuario, [2, 4, 5, 7])) {
            abort(403, 'No autorizado');
        }
    }

    private function validarAccesoConsulta()
    {
        $usuario = Auth::user();

        if (!$this->usuarioAutorizado($usuario, [2, 3, 4, 5, 6, 7])) {
            abort(403, 'No autorizado');
        }
    }

    private function puedeEditarAsignatura(): bool
    {
        $usuario = Auth::user();

        return $this->usuarioAutorizado($usuario, [2, 4, 5, 7]);
    }

    private function validarAccesoEliminar()
    {
        $usuario = Auth::user();

        if (!$usuario || (int) $usuario->idtipoDeUsuario !== 1) {
            abort(403, 'No autorizado');
        }
    }

    private function obtenerAsignaturaDetalle($id)
    {
        $asignatura = DB::table('Asignatura as a')
            ->join('Asignatura_Plan_de_estudios as ape', 'a.idAsignatura', '=', 'ape.idAsignatura')
            ->join('Plan_de_estudios as p', 'ape.idPlanDeEstudios', '=', 'p.idPlanDeEstudios')
            ->join('Nivel_de_formacion as nf', 'ape.idNivelDeFormacion', '=', 'nf.idNivel_de_formacion')
            ->select(
                'a.idAsignatura',
                'a.nombre',
                'ape.claveAsignatura',
                'ape.creditos',
                'ape.semestre',
                'ape.horasConDocente',
                'ape.horasIndependientes',
                'ape.idNivelDeFormacion',
                'nf.nombreNivel',
                'p.idPlanDeEstudios',
                'p.nombrePlanDeEstudios'
            )
            ->where('a.idAsignatura', $id)
            ->first();

        if (!$asignatura) {
            abort(404);
        }

        return $asignatura;
    }

    public function create()
    {
        $this->validarAccesoAlta();

        $planes = PlanDeEstudios::with('licenciatura')
            ->orderBy('nombrePlanDeEstudios')
            ->get();

        $niveles = NivelDeFormacion::orderBy('nombreNivel')->get();

        return view(
            'SGAIDMA.moduloAsignaturas.altaDeAsignatura',
            compact('planes', 'niveles')
        );
    }

    public function store(Request $request)
    {
        $this->validarAccesoAlta();

        $request->validate([
            'nombre' => 'required|string|max:100',
            'claveAsignatura' => 'required|string|max:20',
            'creditos' => 'required|integer|min:1',
            'semestre' => 'required|integer|min:1|max:12',
            'horasConDocente' => 'required|integer|min:0',
            'horasIndependientes' => 'required|integer|min:0',
            'idNivelDeFormacion' => 'required|exists:Nivel_de_formacion,idNivel_de_formacion',
            'idPlanDeEstudios' => 'required|exists:Plan_de_estudios,idPlanDeEstudios',
            'documentoAsignatura' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $existeNombre = Asignatura::where('nombre', $request->nombre)->exists();

        if ($existeNombre) {
            return back()
                ->with('popupError', 'Ya existe una asignatura con ese nombre.')
                ->withInput();
        }

        $existeClave = DB::table('Asignatura_Plan_de_estudios')
            ->where('claveAsignatura', $request->claveAsignatura)
            ->exists();

        if ($existeClave) {
            return back()
                ->with('popupError', 'La clave de la asignatura ya existe.')
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $asignatura = Asignatura::create([
                'nombre' => $request->nombre,
            ]);

            DB::table('Asignatura_Plan_de_estudios')->insert([
                'idAsignatura' => $asignatura->idAsignatura,
                'idPlanDeEstudios' => $request->idPlanDeEstudios,
                'claveAsignatura' => $request->claveAsignatura,
                'creditos' => $request->creditos,
                'semestre' => $request->semestre,
                'horasConDocente' => $request->horasConDocente,
                'horasIndependientes' => $request->horasIndependientes,
                'idNivelDeFormacion' => $request->idNivelDeFormacion,
            ]);

            if ($request->hasFile('documentoAsignatura')) {
                $request->file('documentoAsignatura')
                    ->store('documentos/asignaturas/' . $asignatura->idAsignatura, 'public');
            }

            DB::commit();

            return redirect()
                ->route('altaAsignatura')
                ->with('success', 'Alta de asignatura realizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function index(Request $request)
    {
        $this->validarAccesoConsulta();

        $buscar = $request->buscarAsignatura;
        $orden = $request->orden;
        $puedeEditar = $this->puedeEditarAsignatura();

        $query = DB::table('Asignatura as a')
            ->join('Asignatura_Plan_de_estudios as ape', 'a.idAsignatura', '=', 'ape.idAsignatura')
            ->join('Plan_de_estudios as p', 'ape.idPlanDeEstudios', '=', 'p.idPlanDeEstudios')
            ->select(
                'a.idAsignatura',
                'a.nombre',
                'ape.claveAsignatura',
                'ape.creditos',
                'ape.semestre',
                'p.nombrePlanDeEstudios'
            );

        if ($request->filled('buscarAsignatura')) {
            $query->where(function ($q) use ($buscar) {
                $q->where('a.nombre', 'like', "%{$buscar}%")
                    ->orWhere('ape.claveAsignatura', 'like', "%{$buscar}%")
                    ->orWhere('p.nombrePlanDeEstudios', 'like', "%{$buscar}%");
            });
        }

        $ordenes = [
            'nombre' => ['a.nombre', 'asc'],
            'semestre' => ['ape.semestre', 'asc'],
            'creditos' => ['ape.creditos', 'asc'],
            'plan' => ['p.nombrePlanDeEstudios', 'asc'],
        ];

        if (isset($ordenes[$orden])) {
            $query->orderBy($ordenes[$orden][0], $ordenes[$orden][1]);
        } else {
            $query->orderBy('a.nombre');
        }

        $asignaturas = $query->paginate(10)->withQueryString();

        return view(
            'SGAIDMA.moduloAsignaturas.consultaDeAsignatura',
            compact('asignaturas', 'buscar', 'orden', 'puedeEditar')
        );
    }

    public function show($id)
    {
        $this->validarAccesoConsulta();

        $asignatura = $this->obtenerAsignaturaDetalle($id);
        $puedeEditar = $this->puedeEditarAsignatura();

        return view(
            'SGAIDMA.moduloAsignaturas.detalleDeAsignatura',
            compact('asignatura', 'puedeEditar')
        );
    }

    public function edit($id)
    {
        $this->validarAccesoAlta();

        $asignatura = $this->obtenerAsignaturaDetalle($id);

        $planes = PlanDeEstudios::with('licenciatura')
            ->orderBy('nombrePlanDeEstudios')
            ->get();

        $niveles = NivelDeFormacion::orderBy('nombreNivel')->get();

        return view(
            'SGAIDMA.moduloAsignaturas.altaDeAsignatura',
            compact('asignatura', 'planes', 'niveles')
        );
    }

    public function update(Request $request, $id)
    {
        $this->validarAccesoAlta();

        $request->validate([
            'nombre' => 'required|string|max:100',
            'claveAsignatura' => 'required|string|max:20',
            'creditos' => 'required|integer|min:1',
            'semestre' => 'required|integer|min:1|max:12',
            'horasConDocente' => 'required|integer|min:0',
            'horasIndependientes' => 'required|integer|min:0',
            'idNivelDeFormacion' => 'required|exists:Nivel_de_formacion,idNivel_de_formacion',
            'idPlanDeEstudios' => 'required|exists:Plan_de_estudios,idPlanDeEstudios',
            'documentoAsignatura' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $existeNombre = Asignatura::where('nombre', $request->nombre)
            ->where('idAsignatura', '!=', $id)
            ->exists();

        if ($existeNombre) {
            return back()
                ->with('popupError', 'Ya existe una asignatura con ese nombre.')
                ->withInput();
        }

        $existeClave = DB::table('Asignatura_Plan_de_estudios')
            ->where('claveAsignatura', $request->claveAsignatura)
            ->where('idAsignatura', '!=', $id)
            ->exists();

        if ($existeClave) {
            return back()
                ->with('popupError', 'La clave de la asignatura ya existe.')
                ->withInput();
        }

        DB::beginTransaction();

        try {
            Asignatura::where('idAsignatura', $id)->update([
                'nombre' => $request->nombre,
            ]);

            $relacion = DB::table('Asignatura_Plan_de_estudios')
                ->where('idAsignatura', $id)
                ->first();

            $payload = [
                'idPlanDeEstudios' => $request->idPlanDeEstudios,
                'claveAsignatura' => $request->claveAsignatura,
                'creditos' => $request->creditos,
                'semestre' => $request->semestre,
                'horasConDocente' => $request->horasConDocente,
                'horasIndependientes' => $request->horasIndependientes,
                'idNivelDeFormacion' => $request->idNivelDeFormacion,
            ];

            if ($relacion) {
                DB::table('Asignatura_Plan_de_estudios')
                    ->where('idAsignatura', $id)
                    ->update($payload);
            } else {
                $payload['idAsignatura'] = $id;
                DB::table('Asignatura_Plan_de_estudios')->insert($payload);
            }

            if ($request->hasFile('documentoAsignatura')) {
                $request->file('documentoAsignatura')
                    ->store('documentos/asignaturas/' . $id, 'public');
            }

            DB::commit();

            return redirect()
                ->route('asignaturas.show', $id)
                ->with('success', 'Asignatura actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id)
    {
        $this->validarAccesoEliminar();

        DB::beginTransaction();

        try {
            DB::table('Asignatura_Plan_de_estudios')
                ->where('idAsignatura', $id)
                ->delete();

            Asignatura::where('idAsignatura', $id)->delete();

            DB::commit();

            return redirect()
                ->route('consultaAsignatura')
                ->with('success', 'Asignatura eliminada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
