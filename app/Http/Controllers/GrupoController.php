<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Grupo;
use App\Models\Licenciatura;
use App\Models\Modalidad;

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
        $modalidades = Modalidad::orderBy('nombreModalidad')->get();

        return view(
            'SGAIDMA.moduloGrupos.altaDeGrupo',
            compact('licenciaturas', 'modalidades')
        );
    }

    public function store(Request $request)
    {
        $this->validarAcceso();

        $request->validate([
            'nombreGrupo' => 'required|string|max:45',
            'claveGrupo' => 'required|string|max:45',
            'idLicenciatura' => 'required|exists:Licenciatura,idLicenciatura',
            'semestre' => 'required|integer|min:1|max:12',
            'idModalidad' => 'required|exists:Modalidad,idModalidad',
            'periodoAcademico' => 'required|string|max:60',
        ]);

        $existeNombre = Grupo::where('nombreGrupo', $request->nombreGrupo)->exists();
        if ($existeNombre) {
            return back()
                ->with('popupError', 'Ya existe un grupo con ese nombre.')
                ->withInput();
        }

        $existeClave = Grupo::where('claveGrupo', $request->claveGrupo)->exists();
        if ($existeClave) {
            return back()
                ->with('popupError', 'La clave del grupo ya existe.')
                ->withInput();
        }

        DB::beginTransaction();

        try {
            Grupo::create([
                'nombreGrupo' => $request->nombreGrupo,
                'claveGrupo' => $request->claveGrupo,
                'semestre' => $request->semestre,
                'periodoAcademico' => $request->periodoAcademico,
                'idModalidad' => $request->idModalidad,
                'idLicenciatura' => $request->idLicenciatura,
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
}
