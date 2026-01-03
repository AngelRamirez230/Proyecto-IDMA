<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\SolicitudDeBeca;
use App\Models\TipoDeDocumentacion;
use App\Models\DocumentacionDeUsuario;
use App\Models\Beca;

class SolicitudDeBecaController extends Controller
{
    /**
     * Mostrar formulario de solicitud de beca
     */
    public function create($idBeca)
    {
        $usuario = auth()->user();

        if (!$usuario || !$usuario->estudiante) {
            abort(403, 'Acceso no autorizado');
        }

        $beca = Beca::where('idBeca', $idBeca)
            ->where('idEstatus', 1) // activa
            ->firstOrFail();

        return view('SGFIDMA.moduloSolicitudBeca.formularioSolicitudDeBeca', compact('beca'));
    }

    /**
     * Guardar solicitud de beca
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // 1️⃣ Validación de ambos archivos
            $request->validate([
                'idBeca' => 'required|exists:beca,idBeca',
                'promedio' => 'required|numeric|min:0|max:10',
                'examenExtraordinario' => 'nullable|string|max:255',
                'documento_solicitud' => 'required|file|mimes:pdf|max:5120',
                'documento_adicional' => 'required|file|mimes:pdf|max:5120'
            ]);

            $usuario = auth()->user();
            $estudiante = $usuario->estudiante;

            if (!$estudiante) {
                abort(403, 'El usuario no es estudiante');
            }

            // 2️⃣ Validar solicitud duplicada
            $existeSolicitud = SolicitudDeBeca::where('idEstudiante', $estudiante->idEstudiante)
                ->where('idBeca', $request->idBeca)
                ->whereIn('idEstatus', [5, 6]) // pendiente o aprobada
                ->exists();

            if ($existeSolicitud) {
                return back()
                    ->with('popupError', 'Ya tienes una solicitud registrada para esta beca')
                    ->withInput();
            }

            // 3️⃣ Guardar archivos
            $archivos = [];
            if ($request->hasFile('documento_solicitud')) {
                $archivos['solicitud'] = $request->file('documento_solicitud')->store('documentos/becas', 'public');
            }
            if ($request->hasFile('documento_adicional')) {
                $archivos['adicional'] = $request->file('documento_adicional')->store('documentos/becas', 'public');
            }

            // 4️⃣ Guardar documentos del usuario usando IDs fijos
            $idTipoSolicitud = 1;   // Solicitud de beca
            $idTipoAdicional = 2;   // Documentación adicional

            DocumentacionDeUsuario::create([
                'idUsuario' => $usuario->idUsuario,
                'idTipoDeDocumentacion' => $idTipoSolicitud,
                'ruta' => $archivos['solicitud'] ?? ''
            ]);

            if (isset($archivos['adicional'])) {
                DocumentacionDeUsuario::create([
                    'idUsuario' => $usuario->idUsuario,
                    'idTipoDeDocumentacion' => $idTipoAdicional,
                    'ruta' => $archivos['adicional']
                ]);
            }

            // 5️⃣ Guardar solicitud de beca
            SolicitudDeBeca::create([
                'idEstudiante' => $estudiante->idEstudiante,
                'idBeca' => $request->idBeca,
                'promedioAnterior' => $request->promedio,
                'examenExtraordinario' => $request->examenExtraordinario,
                'observacion' => null,
                'fechaDeSolicitud'=> now(),
                'fechaDeConclusion' => null,
                'idEstatus' => 5 // PENDIENTE
            ]);

            DB::commit();

            return redirect()
                ->route('consultaBeca')
                ->with('success', 'Solicitud de beca enviada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();

            // Eliminar archivos en caso de error
            if (!empty($archivos)) {
                foreach ($archivos as $ruta) {
                    Storage::disk('public')->delete($ruta);
                }
            }

            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }


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

        /* ==========================
        🔍 BÚSQUEDA
        ========================== */
        if ($request->filled('buscarSolicitudDeBeca')) {
            $query->where(function ($q) use ($buscar) {

                // Buscar por nombre de beca
                $q->whereHas('beca', function ($b) use ($buscar) {
                    $b->where('nombreDeBeca', 'LIKE', "%{$buscar}%");
                })

                // Buscar por nombre del estudiante
                ->orWhereHas('estudiante.usuario', function ($u) use ($buscar) {
                    $u->where('primerNombre', 'LIKE', "%{$buscar}%")
                    ->orWhere('segundoNombre', 'LIKE', "%{$buscar}%")
                    ->orWhere('primerApellido', 'LIKE', "%{$buscar}%")
                    ->orWhere('segundoApellido', 'LIKE', "%{$buscar}%");
                });
            });
        }

        /* ==========================
        🧮 FILTRO POR ESTATUS
        ========================== */
        if ($filtro) {
            if ($filtro === 'pendientes') {
                $query->where('idEstatus', 5);
            } elseif ($filtro === 'aprobadas') {
                $query->where('idEstatus', 6);
            } elseif ($filtro === 'rechazadas') {
                $query->where('idEstatus', 7);
            }
        }

        /* ==========================
        ↕️ ORDENAMIENTO
        ========================== */
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


}
