<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\SolicitudDeBeca;
use App\Models\Beca;
use App\Models\DocumentacionSolicitudDeBeca;

class SolicitudDeBecaController extends Controller
{
    /* ======================================================
       FORMULARIO DE SOLICITUD
    ====================================================== */
    public function create($idBeca)
    {
        $usuario = auth()->user();

        if (!$usuario || !$usuario->estudiante) {
            abort(403, 'Acceso no autorizado');
        }

        $beca = Beca::where('idBeca', $idBeca)
            ->where('idEstatus', 1)
            ->firstOrFail();

        return view(
            'SGFIDMA.moduloSolicitudBeca.formularioSolicitudDeBeca',
            compact('beca')
        );
    }

    /* ======================================================
       GUARDAR SOLICITUD
    ====================================================== */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'idBeca' => 'required|exists:beca,idBeca',
                'promedio' => 'required|numeric|min:0|max:10',
                'examenExtraordinario' => 'nullable|string|max:255',
                'documento_solicitud' => 'required|file|mimes:pdf|max:5120',
                'documento_adicional' => 'required|file|mimes:pdf|max:5120',
            ]);

            $usuario = auth()->user();
            $estudiante = $usuario->estudiante;

            if (!$estudiante) {
                abort(403, 'El usuario no es estudiante');
            }

            /* ===============================
               VALIDAR DUPLICADO
            =============================== */
            $existeSolicitud = SolicitudDeBeca::where('idEstudiante', $estudiante->idEstudiante)
                ->where('idBeca', $request->idBeca)
                ->whereIn('idEstatus', [5, 6])
                ->exists();

            if ($existeSolicitud) {
                return back()
                    ->with('popupError', 'Ya tienes una solicitud registrada para esta beca')
                    ->withInput();
            }

            /* ===============================
               CREAR SOLICITUD
            =============================== */
            $solicitud = SolicitudDeBeca::create([
                'idEstudiante' => $estudiante->idEstudiante,
                'idBeca' => $request->idBeca,
                'promedioAnterior' => $request->promedio,
                'examenExtraordinario' => $request->examenExtraordinario,
                'observacion' => null,
                'fechaDeSolicitud' => now(),
                'fechaDeConclusion' => null,
                'idEstatus' => 5,
            ]);

            /* ===============================
               GUARDAR DOCUMENTOS
            =============================== */
            $documentos = [
                'documento_solicitud' => 1,
                'documento_adicional' => 2,
            ];

            foreach ($documentos as $input => $idTipo) {

                if ($request->hasFile($input)) {

                    $ruta = $request->file($input)
                        ->store('documentos/becas', 'public');

                    DocumentacionSolicitudDeBeca::create([
                        'idEstudiante' => $estudiante->idEstudiante,
                        'idSolicitudDeBeca' => $solicitud->idSolicitudDeBeca,
                        'idTipoDeDocumentacion' => $idTipo,
                        'ruta' => $ruta,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('consultaBeca')
                ->with('success', 'Solicitud de beca enviada correctamente');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /* ======================================================
       LISTADO
    ====================================================== */
    public function index(Request $request)
    {
        $orden  = $request->orden;
        $filtro = $request->filtro;
        $buscar = $request->buscarSolicitudDeBeca;

        $query = SolicitudDeBeca::with([
            'estudiante.usuario',
            'beca',
            'estatus'
        ]);

        if ($request->filled('buscarSolicitudDeBeca')) {
            $query->where(function ($q) use ($buscar) {

                $q->whereHas('beca', fn ($b) =>
                    $b->where('nombreDeBeca', 'LIKE', "%{$buscar}%")
                )
                ->orWhereHas('estudiante.usuario', fn ($u) =>
                    $u->where('primerNombre', 'LIKE', "%{$buscar}%")
                      ->orWhere('primerApellido', 'LIKE', "%{$buscar}%")
                );
            });
        }

        if ($filtro) {
            $map = [
                'pendientes' => 5,
                'aprobadas'  => 6,
                'rechazadas' => 7,
            ];
            $query->where('idEstatus', $map[$filtro]);
        }

        if ($orden === 'mas_reciente') {
            $query->orderBy('fechaDeSolicitud', 'desc');
        } elseif ($orden === 'menos_reciente') {
            $query->orderBy('fechaDeSolicitud', 'asc');
        }

        $solicitudes = $query->paginate(10)->withQueryString();

        return view(
            'SGFIDMA.moduloSolicitudBeca.consultaSolicitudDeBeca',
            compact('solicitudes', 'buscar', 'filtro', 'orden')
        );
    }

    /* ======================================================
       EDITAR
    ====================================================== */
    public function edit($id)
    {
        $solicitud = SolicitudDeBeca::with([
            'beca',
            'estatus',
            'documentaciones.tipoDeDocumentacion'
        ])->findOrFail($id);

        $docSolicitud = $solicitud->documentaciones
            ->where('idTipoDeDocumentacion', 1)
            ->first();

        $docAdicional = $solicitud->documentaciones
            ->where('idTipoDeDocumentacion', 2)
            ->first();

        return view(
            'SGFIDMA.moduloSolicitudBeca.modificacionSolicitudDeBeca',
            compact('solicitud', 'docSolicitud', 'docAdicional')
        );
    }

    /* ======================================================
       ACTUALIZAR
    ====================================================== */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $solicitud = SolicitudDeBeca::with('documentaciones')
                ->findOrFail($id);

            $request->validate([
                'promedio' => 'required|numeric|min:8.5|max:10',
                'examenExtraordinario' => 'nullable|string|max:255',
                'documento_solicitud' => 'nullable|file|mimes:pdf|max:5120',
                'documento_adicional' => 'nullable|file|mimes:pdf|max:5120',
            ]);

            $solicitud->update([
                'promedioAnterior' => $request->promedio,
                'examenExtraordinario' => $request->examenExtraordinario,
            ]);

            $documentos = [
                'documento_solicitud' => 1,
                'documento_adicional' => 2,
            ];

            foreach ($documentos as $input => $idTipo) {

                if ($request->hasFile($input)) {

                    $doc = $solicitud->documentaciones
                        ->where('idTipoDeDocumentacion', $idTipo)
                        ->first();

                    if ($doc && Storage::disk('public')->exists($doc->ruta)) {
                        Storage::disk('public')->delete($doc->ruta);
                    }

                    $ruta = $request->file($input)
                        ->store('documentos/becas', 'public');

                    if ($doc) {
                        $doc->update(['ruta' => $ruta]);
                    } else {
                        DocumentacionSolicitudDeBeca::create([
                            'idEstudiante' => $solicitud->idEstudiante,
                            'idSolicitudDeBeca' => $solicitud->idSolicitudDeBeca,
                            'idTipoDeDocumentacion' => $idTipo,
                            'ruta' => $ruta,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('consultaSolicitudBeca')
                ->with('success', 'Solicitud actualizada correctamente');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
}
