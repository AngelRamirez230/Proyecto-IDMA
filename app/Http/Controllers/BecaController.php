<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beca;
use App\Models\Usuario;

class BecaController extends Controller
{
    public function create()
    {
        return view('SGFIDMA.moduloBecas.altaDeBeca');
    }

    public function store(Request $request)
    {
        // Validaciones básicas
        $request->validate([
            'nombreBeca' => 'required|string',
            'porcentajeBeca' => 'required|numeric|min:1|max:100',
        ]);

        // Formatear nombre
        $nombre = $this->mbUcwords($request->nombreBeca);

        // Validar si ya existe un registro idéntico
        $existe = Beca::whereRaw('LOWER(nombreDeBeca) = ?', [mb_strtolower($request->nombreBeca)])
                    ->where('porcentajeDeDescuento', $request->porcentajeBeca)
                    ->exists();

        if ($existe) {
            return back()
                ->with('popupError', 'Ya existe una beca con ese nombre y porcentaje.')
                ->withInput();
        }

        try {

            // Intentar guardar el registro
            Beca::create([
                'nombreDeBeca' => $nombre,
                'porcentajeDeDescuento' => $request->porcentajeBeca,
                'idEstatus' => 1
            ]);

            return redirect()->route('altaBeca')
                            ->with('success', 'Beca registrada correctamente');

        } catch (\Exception $e) {

            // Error inesperado → NO se guardó
            return back()
                ->with('popupError', 'Error: No se pudo registrar la beca. Inténtalo nuevamente.')
                ->withInput();
        }
    }



    private function mbUcwords($string, $encoding = 'UTF-8')
    {
        $string = mb_strtolower($string, $encoding);
        $words = explode(' ', $string);

        foreach ($words as &$word) {
            if ($word !== '') {
                $first = mb_substr($word, 0, 1, $encoding);
                $rest = mb_substr($word, 1, null, $encoding);
                $word = mb_strtoupper($first, $encoding) . $rest;
            }
        }

        return implode(' ', $words);
    }

    public function index(Request $request)
    {
        $orden  = $request->orden;
        $filtro = $request->filtro;
        $buscar = $request->buscarBeca;

        
        $usuario = null;
        if (session()->has('idUsuario')) {
            $usuario = \App\Models\Usuario::find(session('idUsuario'));
        }

        $query = Beca::with('estatus');

        
        if ($usuario && $usuario->idtipoDeUsuario == 4) {
            $query->where('idEstatus', 1);
        }

        
        if ($request->filled('buscarBeca')) {
            $query->where('nombreDeBeca', 'LIKE', '%' . $buscar . '%');
        }

        
        if ($filtro === 'activas') {
            $query->where('idEstatus', 1);
        } elseif ($filtro === 'suspendidas') {
            $query->where('idEstatus', 2);
        }

        
        if ($orden === 'alfabetico') {
            $query->orderBy('nombreDeBeca', 'asc');
        } elseif ($orden === 'porcentaje_mayor') {
            $query->orderBy('porcentajeDeDescuento', 'desc');
        } elseif ($orden === 'porcentaje_menor') {
            $query->orderBy('porcentajeDeDescuento', 'asc');
        }

        $becas = $query->paginate(5)->withQueryString();

        return view('SGFIDMA.moduloBecas.consultaDeBeca', compact('becas', 'orden', 'filtro', 'buscar')
        );
    }


    public function edit($id)
    {
        // Buscar la beca por id
        $beca = Beca::findOrFail($id);

        // Retornar la vista de modificación con los datos de la beca
        return view('SGFIDMA.moduloBecas.modificacionDeBeca', compact('beca'));
    }


    public function update(Request $request, $idBeca)
    {
        $beca = Beca::findOrFail($idBeca);

        if ($request->accion === 'guardar') {
            // Validar y guardar cambios
            $request->validate([
                'porcentajeBeca' => 'required|numeric|min:1|max:100',
            ]);

            $beca->porcentajeDeDescuento = $request->porcentajeBeca;
            $beca->save();

            return redirect()->route('consultaBeca')->with('success', 'Beca actualizada correctamente.');

        } elseif ($request->accion === 'Suspender/Habilitar') {
            // Guardar el estatus actual antes de cambiarlo
            $estatusAnterior = $beca->idEstatus;

            // Alternar estatus
            $beca->idEstatus = ($beca->idEstatus == 1) ? 2 : 1;
            $beca->save();

            // Determinar mensaje según el cambio
            $mensaje = ($estatusAnterior == 1) 
                ? "La beca {$beca->nombreDeBeca} ha sido suspendida." 
                : "La beca {$beca->nombreDeBeca} ha sido activada.";

            return redirect()->route('consultaBeca')->with('success', $mensaje);
        }
    }


    public function destroy($idBeca)
    {
        $beca = Beca::findOrFail($idBeca);
        $beca->delete();

        return redirect()->route('consultaBeca')->with('success', "La beca {$beca->nombreDeBeca} ha sido eliminada.");
    }



    






    
}
