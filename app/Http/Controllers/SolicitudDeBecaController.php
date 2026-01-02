<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SolicitudDeBeca;
use App\Models\TipoDeDocumentacion;
use App\Models\DocumentacionDeUsuario;

class SolicitudDeBecaController extends Controller
{
    /**
     * Guardar solicitud de beca
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            /* =========================================
               1️⃣ VALIDACIÓN
            ========================================= */
            $request->validate([
                'idBeca' => 'required|exists:beca,idBeca',
                'promedio' => 'required|numeric|min:0|max:10',
                'examenExtraordinario' => 'nullable|string|max:255',
                'documento' => 'required|file|mimes:pdf|max:5120'
            ]);

            /* =========================================
               2️⃣ USUARIO Y ESTUDIANTE
            ========================================= */
            $usuario = auth()->user();
            $estudiante = $usuario->estudiante;

            if (!$estudiante) {
                abort(403, 'El usuario no es estudiante');
            }

            /* =========================================
               3️⃣ SUBIR DOCUMENTO PDF
            ========================================= */
            $rutaArchivo = $request->file('documento')
                ->store('documentos/becas', 'public');

            /* =========================================
               4️⃣ OBTENER TIPO DE DOCUMENTACIÓN
            ========================================= */
            $tipoDocumento = TipoDeDocumentacion::where('nombreDocumento', 'Solicitud de beca')
                ->first();

            if (!$tipoDocumento) {
                throw new \Exception('No existe el tipo de documento "Solicitud de beca"');
            }

            /* =========================================
               5️⃣ GUARDAR DOCUMENTACIÓN
            ========================================= */
            DocumentacionDeUsuario::create([
                'idUsuario' => $usuario->idUsuario,
                'idTipoDeDocumento' => $tipoDocumento->idTipoDeDocumentacion,
                'ruta' => $rutaArchivo
            ]);

            /* =========================================
               6️⃣ GUARDAR SOLICITUD DE BECA
            ========================================= */
            SolicitudDeBeca::create([
                'idEstudiante' => $estudiante->idEstudiante,
                'idBeca' => $request->idBeca,
                'promedioAnterior' => $request->promedio,
                'examenExtraordinario' => $request->examenExtraordinario,
                'observacion' => null,
                'idEstatus' => 1 // PENDIENTE
            ]);

            DB::commit();

            return redirect()
                ->route('consultaBeca')
                ->with('success', 'Solicitud de beca enviada correctamente');

        } catch (\Exception $e) {

            DB::rollBack();

            if (isset($rutaArchivo)) {
                Storage::disk('public')->delete($rutaArchivo);
            }

            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
}
