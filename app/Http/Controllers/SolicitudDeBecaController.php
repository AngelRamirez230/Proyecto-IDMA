<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Estudiante;
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
        $usuario = Auth::user();

        // Solo estudiantes pueden solicitar beca
        if (!$usuario || !$usuario->estudiante) {
            abort(403, 'Acceso no autorizado');
        }

        // Beca activa
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
                'idBeca' => 'required|exists:beca,idBeca', // ajusta si tu tabla es "becas"
                'promedio' => 'required|numeric|min:8.5|max:10',
                'examenExtraordinario' => 'nullable|string|max:255',
                'documento_solicitud' => 'required|file|mimes:pdf|max:5120',
                'documento_adicional' => 'nullable|file|mimes:pdf|max:5120',
            ]);

            $usuario = Auth::user();
            $estudiante = $usuario->estudiante;

            if (!$estudiante) {
                abort(403, 'El usuario no es estudiante');
            }

            /* ======================================================
            VALIDAR DUPLICADO (SOLO PENDIENTE)
            ====================================================== */
            $existeSolicitud = SolicitudDeBeca::delEstudiante($estudiante->idEstudiante)
                ->where('idBeca', $request->idBeca)
                ->where('idEstatus', 5) // SOLO pendiente
                ->exists();
            
            
            
            
            if ($existeSolicitud) {
                return back()
                    ->with('popupError', 'Ya tienes una solicitud pendiente para esta beca')
                    ->withInput();
            }

            /* ======================================================
            CREAR SOLICITUD
            ====================================================== */
            $solicitud = SolicitudDeBeca::create([
                'idEstudiante' => $estudiante->idEstudiante,
                'idBeca' => $request->idBeca,
                'promedioAnterior' => $request->promedio,
                'examenExtraordinario' => $request->examenExtraordinario,
                'observacion' => null,
                'fechaDeSolicitud' => now(),
                'fechaDeConclusion' => null,
                'idEstatus' => 5, // Pendiente
            ]);

            /* ======================================================
            GUARDAR DOCUMENTOS
            ====================================================== */
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
                ->route('consultaSolicitudBeca')
                ->with('success', 'Solicitud de beca enviada correctamente');

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;

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

        $usuario = Auth::user();

        $query = SolicitudDeBeca::with([
            'estudiante.usuario',
            'beca',
            'estatus'
        ]);

        /* ======================================================
        🔐 FILTRO REAL POR USUARIO LOGUEADO
        ====================================================== */
        if ($usuario->estudiante) {
            $query->whereHas('estudiante', function ($q) use ($usuario) {
                $q->where('idUsuario', $usuario->idUsuario);
            });
        }

        /* ======================================================
        🔎 BÚSQUEDA
        ====================================================== */
        if ($request->filled('buscarSolicitudDeBeca')) {
            $query->whereHas('beca', function ($b) use ($buscar) {
                $b->where('nombreDeBeca', 'LIKE', "%{$buscar}%");
            });
        }

        /* ======================================================
        📌 FILTRO POR ESTATUS
        ====================================================== */
        if ($filtro && $filtro !== 'todas') {

            $map = [
                'pendientes' => 5,
                'aprobadas'  => 6,
                'rechazadas' => 7,
            ];

            if (isset($map[$filtro])) {
                $query->where('idEstatus', $map[$filtro]);
            }
        }

        /* ======================================================
        ⏱ ORDEN
        ====================================================== */
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
        $usuario = Auth::user();

        $query = SolicitudDeBeca::with([
            'beca',
            'estatus',
            'documentaciones.tipoDeDocumentacion'
        ]);

        if ($usuario->idtipoDeUsuario == 4) {
            $query->delEstudiante($usuario->estudiante->idEstudiante);
        }

        $solicitud = $query->findOrFail($id);

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

            $usuario = Auth::user();

            $request->validate([
                'observaciones' => 'nullable|string|max:200',
            ]);

            /* ======================================================
            VALIDAR ACCIÓN
            ====================================================== */
            if (!$request->filled('accion')) {
                abort(400, 'Acción no definida');
            }

            /* ======================================================
            ADMIN – APROBAR
            ====================================================== */
            if ($request->accion === 'aprobar') {

                if ($usuario->idtipoDeUsuario != 1) {
                    abort(403, 'No autorizado');
                }

                $solicitud = SolicitudDeBeca::findOrFail($id);


                $solicitud->update([
                    'idEstatus' => 6,
                    'fechaDeConclusion' => now(),
                    'observacion' => null,
                ]);

                DB::commit();

                return redirect()
                    ->route('consultaSolicitudBeca')
                    ->with('success', 'Solicitud aprobada correctamente');
            }

            /* ======================================================
            ADMIN – RECHAZAR
            ====================================================== */
            if ($request->accion === 'rechazar') {

                if ($usuario->idtipoDeUsuario != 1) {
                    abort(403, 'No autorizado');
                }

                $solicitud = SolicitudDeBeca::findOrFail($id);


                $solicitud->update([
                    'idEstatus' => 7,
                    'fechaDeConclusion' => now(),
                    'observacion' => $request->observaciones,
                ]);

                DB::commit();

                return redirect()
                    ->route('consultaSolicitudBeca')
                    ->with('success', 'Solicitud rechazada correctamente');
            }

            /* ======================================================
            ESTUDIANTE – GUARDAR CAMBIOS
            ====================================================== */
            if ($request->accion === 'guardar') {

                if ($usuario->idtipoDeUsuario != 4) {
                    abort(403, 'No autorizado');
                }

                $request->validate([
                    'promedio' => 'required|numeric|min:8.5|max:10',
                    'examenExtraordinario' => 'nullable|string|max:255',
                    'documento_solicitud' => 'nullable|file|mimes:pdf|max:5120',
                    'documento_adicional' => 'nullable|file|mimes:pdf|max:5120',
                ]);

                $solicitud = SolicitudDeBeca::where('idSolicitudDeBeca', $id)
                    ->where('idEstudiante', $usuario->estudiante->idEstudiante)
                    ->with('documentaciones')
                    ->firstOrFail();

                $solicitud->update([
                    'promedioAnterior' => $request->promedio,
                    'examenExtraordinario' => $request->examenExtraordinario,
                    'observacion' => null,
                    'idEstatus'=>5,
                    'fechaDeSolicitud' => now(),
                ]);

                /* ===============================
                    ACTUALIZAR DOCUMENTOS
                    =============================== */
                    $tipos = [
                        'documento_solicitud' => 1,
                        'documento_adicional' => 2,
                    ];

                    foreach ($tipos as $input => $idTipo) {

                        if ($request->hasFile($input)) {

                            $archivo = $request->file($input);
                            $ruta = $archivo->store('documentos/becas', 'public');

                            $documento = $solicitud->documentaciones
                                ->where('idTipoDeDocumentacion', $idTipo)
                                ->first();

                            if ($documento) {
                                // 🔁 Reemplazar
                                $documento->update([
                                    'ruta' => $ruta,
                                ]);
                            } else {
                                // ➕ Crear si no existía
                                DocumentacionSolicitudDeBeca::create([
                                    'idEstudiante' => $usuario->estudiante->idEstudiante,
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
                    ->with('success', 'Cambios guardados correctamente');
            }

            abort(400, 'Acción inválida');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }




}
