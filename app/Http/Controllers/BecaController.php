<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Beca;
use App\Models\Usuario;



class BecaController extends Controller
{
    public function create()
    {
        try {
            return view('SGFIDMA.moduloBecas.altaDeBeca');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('popupError', 'Ocurrió un error al cargar la página.');
        }
    }




   public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                // ======================
                // BECA
                // ======================
                'nombreBeca'     => 'required|string|max:150',
                'porcentajeBeca' => 'required|numeric|min:1|max:100',
            ],
            [
                // ======================
                // MENSAJES GENERALES
                // ======================
                'required' => 'El campo :attribute es obligatorio.',
                'string'   => 'El campo :attribute debe ser texto.',
                'numeric'  => 'El campo :attribute debe ser un número válido.',
                'min'      => 'El campo :attribute debe ser mayor o igual a :min.',
                'max'      => 'El campo :attribute no debe exceder :max.',
            ],
            [
                // ======================
                // NOMBRES AMIGABLES
                // ======================
                'nombreBeca'     => 'nombre de la beca',
                'porcentajeBeca' => 'porcentaje de descuento',
            ]
        );

        if ($validator->fails()) {
            return back()
                ->with('popupError', 'No se pudo registrar la beca. Verifica los datos ingresados.')
                ->withErrors($validator)
                ->withInput();
        }

        // Formatear nombre
        $nombre = $this->mbUcwords($request->nombreBeca);

        // Validar duplicados (nombre + porcentaje)
        $existe = Beca::whereRaw('LOWER(nombreDeBeca) = ?', [mb_strtolower($request->nombreBeca)])
                    ->where('porcentajeDeDescuento', $request->porcentajeBeca)
                    ->exists();

        if ($existe) {
            return back()
                ->with('popupError', 'Ya existe una beca con ese nombre y porcentaje.')
                ->withInput();
        }

        try {
            // Guardar beca
            Beca::create([
                'nombreDeBeca' => $nombre,
                'porcentajeDeDescuento' => $request->porcentajeBeca,
                'idEstatus' => 1
            ]);

            return redirect()
                ->route('altaBeca')
                ->with('success', 'Beca registrada correctamente');

        } catch (\Exception $e) {
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
        try {

            $orden  = $request->orden;
            $filtro = $request->filtro;
            $buscar = $request->buscarBeca;

            // Usuario desde sesión
            $usuario = null;
            if (session()->has('idUsuario')) {
                $usuario = \App\Models\Usuario::find(session('idUsuario'));
            }

            // Si no hay usuario, redirigir
            if (!$usuario) {
                return redirect()->route('login')
                    ->with('error', 'Debes iniciar sesión para acceder a las becas.');
            }

            $query = Beca::with('estatus');

            // Estudiante: solo becas activas
            if ((int) $usuario->idtipoDeUsuario === 4) {
                $query->where('idEstatus', 1);
            }

            // Buscar por nombre
            if ($request->filled('buscarBeca')) {
                $query->where('nombreDeBeca', 'LIKE', "%{$buscar}%");
            }

            // Filtros
            if ($filtro === 'activas') {
                $query->where('idEstatus', 1);
            } elseif ($filtro === 'suspendidas') {
                $query->where('idEstatus', 2);
            }

            // Ordenamientos
            if ($orden === 'alfabetico') {
                $query->orderBy('nombreDeBeca', 'asc');
            } elseif ($orden === 'porcentaje_mayor') {
                $query->orderBy('porcentajeDeDescuento', 'desc');
            } elseif ($orden === 'porcentaje_menor') {
                $query->orderBy('porcentajeDeDescuento', 'asc');
            }

            $becas = $query->paginate(10)->withQueryString();

            return view(
                'SGFIDMA.moduloBecas.consultaDeBeca',
                compact('becas', 'orden', 'filtro', 'buscar')
            );

        } catch (\Throwable $e) {

            // Log del error (MUY recomendado)
            \Log::error('Error al cargar becas', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('popupError', 'Ocurrió un error al cargar la lista de becas. Intenta más tarde.');
        }
    }



    public function edit($id)
    {
        try {

            // Buscar la beca por id
            $beca = Beca::findOrFail($id);

            // Retornar la vista de modificación
            return view('SGFIDMA.moduloBecas.modificacionDeBeca', compact('beca'));

        } catch (\Throwable $e) {

            \Log::error('Error al cargar edición de beca', [
                'id_beca' => $id,
                'error'   => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('popupError', 'Ocurrió un error al cargar la información de la beca.');
        }
    }



    public function update(Request $request, $idBeca)
    {
        try {
            $beca = Beca::findOrFail($idBeca);

            if ($request->accion === 'guardar') {

                // Validar nombre y porcentaje
                $validator = Validator::make(
                    $request->all(),
                    [
                        'nombreBeca'     => 'required|string|max:150',
                        'porcentajeBeca' => 'required|numeric|min:1|max:100',
                    ],
                    [
                        'required' => 'El campo :attribute es obligatorio.',
                        'string'   => 'El campo :attribute debe ser texto.',
                        'numeric'  => 'El campo :attribute debe ser un número válido.',
                        'min'      => 'El campo :attribute debe ser mayor o igual a :min.',
                        'max'      => 'El campo :attribute no debe exceder :max.',
                    ],
                    [
                        'nombreBeca'     => 'nombre de la beca',
                        'porcentajeBeca' => 'porcentaje de descuento',
                    ]
                );

                if ($validator->fails()) {
                    return back()
                        ->with('popupError', 'No se pudo actualizar la beca. Verifica los datos ingresados.')
                        ->withErrors($validator)
                        ->withInput();
                }

                // Actualizar campos
                $beca->nombreDeBeca = $request->nombreBeca;
                $beca->porcentajeDeDescuento = $request->porcentajeBeca;
                $beca->save();

                return redirect()->route('consultaBeca')->with('success', 'Beca actualizada correctamente.');

            } elseif ($request->accion === 'Suspender/Habilitar') {

                // Revisar si hay estudiantes usando la beca y evitar suspender
                $tieneSolicitudesActivas = $beca->solicitudes()
                                                ->where('idEstatus', 6)
                                                ->exists();

                if ($beca->idEstatus == 1 && $tieneSolicitudesActivas) {
                    return redirect()->route('consultaBeca')
                        ->with('popupError', 'No se puede suspender esta beca porque algunos estudiantes tienen asignada esta beca.');
                }

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
        } catch (\Throwable $e) {
            \Log::error('Error al actualizar beca', [
                'id_beca' => $idBeca,
                'error'   => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('popupError', 'No se pudo realizar la actualización de la beca.');
        }
    }



    public function destroy($idBeca)
    {
        try {

            $beca = Beca::findOrFail($idBeca);

            // Si algún estudiante la está usando actualmente
            $tieneSolicitudesActivas = $beca->solicitudes()
                ->where('idEstatus', 6) // estatus activo
                ->exists();

            if ($tieneSolicitudesActivas) {
                return redirect()->route('consultaBeca')
                    ->with('popupError', 'No se puede eliminar la beca porque actualmente hay estudiantes que la utilizan.');
            }

            // Si alguna vez fue utilizada
            $tieneHistorial = $beca->solicitudes()->exists();

            if ($tieneHistorial) {
                return redirect()->route('consultaBeca')
                    ->with('popupError', 'No se puede eliminar la beca porque tiene historial de uso por estudiantes, se sugiere solo suspenderla.');
            }

            // Eliminar beca
            $nombreBeca = $beca->nombreDeBeca;
            $beca->delete();

            return redirect()->route('consultaBeca')
                ->with('success', "La beca {$nombreBeca} ha sido eliminada correctamente.");

        } catch (\Throwable $e) {

            \Log::error('Error al eliminar beca', [
                'id_beca' => $idBeca,
                'error'   => $e->getMessage()
            ]);

            return redirect()->route('consultaBeca')
                ->with('popupError', 'Ocurrió un error al intentar eliminar la beca, puede ser que esta beca ya ha sido eliminada por otro usuario.');
        }
    }

    
}
