<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use App\Models\Estudiante;
use App\Models\SolicitudDeBeca;
use App\Models\Beca;
use App\Models\DocumentacionSolicitudDeBeca;
use App\Models\Notificacion;

class SolicitudDeBecaController extends Controller
{


    private function fueraDePeriodoDeSolicitud(): bool
    {
        $hoy = Carbon::now();

        $anio = $hoy->year;

        // Última semana de febrero
        $inicioFebrero = Carbon::create($anio, 2, 1)->endOfMonth()->subDays(6);
        $finFebrero = Carbon::create($anio, 2, 1)->endOfMonth();

        // Última semana de agosto
        $inicioAgosto = Carbon::create($anio, 8, 1)->endOfMonth()->subDays(6);
        $finAgosto = Carbon::create($anio, 8, 1)->endOfMonth();

        return !(
            $hoy->between($inicioFebrero, $finFebrero) ||
            $hoy->between($inicioAgosto, $finAgosto)
        );
    }

    /* ======================================================
       FORMULARIO DE SOLICITUD
    ====================================================== */
    public function create($idBeca)
    {
        try {

            $usuario = Auth::user();

            // =============================
            // SOLO ESTUDIANTES
            // =============================
            if (!$usuario || !$usuario->estudiante) {
                abort(403, 'Acceso no autorizado');
            }

            // =============================
            // VALIDAR PERIODO DE SOLICITUD
            // =============================
            if ($this->fueraDePeriodoDeSolicitud()) {
                return redirect()
                    ->route('consultaBeca')
                    ->with(
                        'popupError',
                        'No te encuentras en el periodo válido para solicitar una beca.'
                    );
            }

            // =============================
            // BECA ACTIVA
            // =============================
            $beca = Beca::where('idBeca', $idBeca)
                ->where('idEstatus', 1)
                ->firstOrFail();

            // =============================
            // VISTA
            // =============================
            return view(
                'SGFIDMA.moduloSolicitudBeca.formularioSolicitudDeBeca',
                compact('beca')
            );

        } catch (\Throwable $e) {

            \Log::error('Error al cargar formulario de solicitud de beca', [
                'idBeca' => $idBeca,
                'error'  => $e->getMessage(),
            ]);

            return redirect()
                ->route('consultaBeca')
                ->with(
                    'popupError',
                    'Ocurrió un error al cargar el formulario de solicitud de beca.'
                );
        }
    }



    /* ======================================================
       GUARDAR SOLICITUD
    ====================================================== */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            /* ======================================================
            VALIDACIONES
            ====================================================== */
            $request->validate(
                [
                    'idBeca' => 'required|exists:beca,idBeca',
                    'promedio' => 'required|numeric|between:8.5,10',
                    'examenExtraordinario' => 'nullable|string|max:255',
                    'documento_solicitud' => 'required|file|mimes:pdf|max:5120',
                    'documento_adicional' => 'nullable|file|mimes:pdf|max:5120',
                ],
                [
                    'idBeca.required' => 'No se recibió la información de la beca.',
                    'idBeca.exists' => 'La beca seleccionada no es válida.',

                    'promedio.required' => 'Debes ingresar tu promedio.',
                    'promedio.numeric' => 'El promedio debe ser un número.',
                    'promedio.between' => 'El promedio debe estar entre 8.5 y 10.',

                    'examenExtraordinario.max' => 'El campo de examen extraordinario es demasiado largo.',

                    'documento_solicitud.required' => 'Debes subir el documento de solicitud.',
                    'documento_solicitud.mimes' => 'El documento de solicitud debe ser un archivo PDF.',
                    'documento_solicitud.max' => 'El documento de solicitud no debe exceder 5 MB.',

                    'documento_adicional.mimes' => 'El documento adicional debe ser un archivo PDF.',
                    'documento_adicional.max' => 'El documento adicional no debe exceder 5 MB.',
                ]
            );

            /* ======================================================
            OBTENER ESTUDIANTE
            ====================================================== */
            $usuario = Auth::user();
            $estudiante = $usuario->estudiante;

            if (!$estudiante) {
                abort(403, 'El usuario no está registrado como estudiante.');
            }

            /* ======================================================
            VALIDAR QUE NO TENGA OTRA BECA APROBADA
            ====================================================== */
            $becaAprobada = SolicitudDeBeca::delEstudiante($estudiante->idEstudiante)
                ->where('idEstatus', 6) // Aprobada
                ->exists();

            if ($becaAprobada) {
                return back()
                    ->with('popupError', 'No puedes solicitar otra beca mientras tengas una beca aprobada vigente.')
                    ->withInput();
            }

            /* ======================================================
            VALIDAR SOLICITUD DUPLICADA (SOLO PENDIENTE)
            ====================================================== */
            $solicitudPendiente = SolicitudDeBeca::delEstudiante($estudiante->idEstudiante)
                ->where('idBeca', $request->idBeca)
                ->where('idEstatus', 5) // Pendiente
                ->exists();

            if ($solicitudPendiente) {
                return back()
                    ->with('popupError', 'Ya tienes una solicitud pendiente para esta beca.')
                    ->withInput();
            }

            /* ======================================================
            CREAR SOLICITUD DE BECA
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
            $tiposDocumentos = [
                'documento_solicitud' => 1,
                'documento_adicional' => 2,
            ];

            foreach ($tiposDocumentos as $campo => $idTipo) {

                if ($request->hasFile($campo)) {

                    $ruta = $request->file($campo)
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

            /* ======================================================
            REDIRECCIÓN FINAL
            ====================================================== */
            return redirect()
                ->route('consultaSolicitudBeca')
                ->with('success', 'Tu solicitud de beca fue enviada correctamente.');

        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;

            return back()
                ->with('popupError', 'Ocurrió un error al enviar tu solicitud.')
                ->withInput();
        }
    }



    /* ======================================================
    LISTADO
    ====================================================== */
    public function index(Request $request)
    {
        try {

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
                FILTRO REAL POR USUARIO LOGUEADO
            ====================================================== */
            if ($usuario->estudiante) {
                $query->whereHas('estudiante', function ($q) use ($usuario) {
                    $q->where('idUsuario', $usuario->idUsuario);
                });
            }

            /* ======================================================
                BÚSQUEDA
            ====================================================== */
            if ($request->filled('buscarSolicitudDeBeca')) {
                $buscar = trim($buscar);

                $query->whereHas('beca', function ($b) use ($buscar) {
                    $b->where('nombreDeBeca', 'LIKE', "%{$buscar}%");
                });
            }

            /* ======================================================
                FILTRO POR ESTATUS
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
                ORDEN
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

        } catch (\Throwable $e) {

            \Log::error('Error al consultar solicitudes de beca', [
                'usuario_id' => Auth::id(),
                'error'      => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with(
                    'popupError',
                    'Ocurrió un error al consultar la lista de solicitudes de beca.'
                );
        }
    }




    /* ======================================================
       EDITAR
    ====================================================== */
    public function edit($id)
    {
        try {
            $usuario = Auth::user();

            $query = SolicitudDeBeca::with([
                'beca',
                'estatus',
                'documentaciones.tipoDeDocumentacion'
            ]);

            // Restricción para estudiante
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

        } catch (\Exception $e) {

            return redirect()
                ->route('consultaSolicitudDeBeca')
                ->with('popupError', 'Ocurrió un error al intentar editar la solicitud de beca.');
        }
    }




    private function calcularFechaConclusion(Carbon $fechaSolicitud): Carbon
    {
        $anio = $fechaSolicitud->year;

        // Última semana de febrero
        $inicioUltimaSemanaFebrero = Carbon::create($anio, 2, 1)
            ->endOfMonth()
            ->subDays(6);

        $finUltimaSemanaFebrero = Carbon::create($anio, 2, 1)
            ->endOfMonth();

        // Última semana de agosto
        $inicioUltimaSemanaAgosto = Carbon::create($anio, 8, 1)
            ->endOfMonth()
            ->subDays(6);

        $finUltimaSemanaAgosto = Carbon::create($anio, 8, 1)
            ->endOfMonth();

        // Si fue solicitada en febrero → termina antes de agosto
        if ($fechaSolicitud->between($inicioUltimaSemanaFebrero, $finUltimaSemanaFebrero)) {

            return $inicioUltimaSemanaAgosto->subDays(2); // 1 o 2 días antes
        }

        // Si fue solicitada en agosto → termina antes de febrero (siguiente año)
        if ($fechaSolicitud->between($inicioUltimaSemanaAgosto, $finUltimaSemanaAgosto)) {

            return Carbon::create($anio + 1, 2, 1)
                ->endOfMonth()
                ->subDays(2); // 1–2 días antes de la última semana
        }

        // Fallback (no debería pasar)
        return Carbon::now();
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

                if (!Auth::user()->esAdmin() && !Auth::user()->esEmpleadoDe(11)) {
                    abort(403, 'No autorizado');
                }

                $solicitud = SolicitudDeBeca::findOrFail($id);

                $fechaSolicitud = Carbon::parse($solicitud->fechaDeSolicitud);

                $fechaConclusion = $this->calcularFechaConclusion($fechaSolicitud);

                $solicitud->update([
                    'idEstatus' => 6, // Aprobada
                    'fechaDeConclusion' => $fechaConclusion,
                    'observacion' => null,
                ]);


                // ===== NOTIFICACIÓN =====
                $usuario = $solicitud->estudiante?->usuario;
                $nombreBeca = $solicitud->beca?->nombreDeBeca ?? 'tu beca';
                if ($usuario) {
                    Notificacion::create([
                        'idUsuario'          => $usuario->idUsuario,
                        'titulo'             => 'Solicitud de beca aprobada',
                        'mensaje'            => "Tu solicitud para la beca: {$nombreBeca} ha sido aprobada. Fecha de conclusión: {$fechaConclusion->format('d/m/Y')}.",
                        'tipoDeNotificacion' => 1, // Información
                        'fechaDeInicio'      => Carbon::today()->toDateString(),
                        'fechaFin'           => Carbon::today()->addDays(3)->toDateString(),
                        'leida'              => 0,
                    ]);
                }

                DB::commit();

                return redirect()
                    ->route('consultaSolicitudBeca')
                    ->with('success', 'Solicitud aprobada correctamente');
            }

            /* ======================================================
            ADMIN – RECHAZAR
            ====================================================== */
            if ($request->accion === 'rechazar') {

                if (!Auth::user()->esAdmin() && !Auth::user()->esEmpleadoDe(11)) {
                    abort(403, 'No autorizado');
                }

                $solicitud = SolicitudDeBeca::findOrFail($id);


                $solicitud->update([
                    'idEstatus' => 7,
                    'fechaDeConclusion' => now(),
                    'observacion' => $request->observaciones,
                ]);

                // ===== NOTIFICACIÓN =====
                $usuario = $solicitud->estudiante?->usuario;
                $nombreBeca = $solicitud->beca?->nombreDeBeca ?? 'tu beca';
                if ($usuario) {
                    Notificacion::create([
                        'idUsuario'          => $usuario->idUsuario,
                        'titulo'             => 'Solicitud de beca rechazada',
                        'mensaje'            => "Tu solicitud para la beca '{$nombreBeca}' ha sido rechazada. Observaciones: {$request->observaciones}",
                        'tipoDeNotificacion' => 2, // Advertencia
                        'fechaDeInicio'      => Carbon::today()->toDateString(),
                        'fechaFin'           => Carbon::today()->addDays(7)->toDateString(),
                        'leida'              => 0,
                    ]);
                }

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


                /* ======================================================
                VALIDACIONES
                ====================================================== */
                $request->validate(
                    [
                        'idBeca' => 'required|exists:beca,idBeca',
                        'promedio' => 'required|numeric|between:8.5,10',
                        'examenExtraordinario' => 'nullable|string|max:255',
                    ],
                    [
                        'idBeca.required' => 'No se recibió la información de la beca.',
                        'idBeca.exists' => 'La beca seleccionada no es válida.',

                        'promedio.required' => 'Debes ingresar tu promedio.',
                        'promedio.numeric' => 'El promedio debe ser un número.',
                        'promedio.between' => 'El promedio debe estar entre 8.5 y 10.',

                        'examenExtraordinario.max' => 'El campo de examen extraordinario es demasiado largo.',

                        'documento_solicitud.mimes' => 'El documento de solicitud debe ser un archivo PDF.',
                        'documento_solicitud.max' => 'El documento de solicitud no debe exceder 5 MB.',

                        'documento_adicional.mimes' => 'El documento adicional debe ser un archivo PDF.',
                        'documento_adicional.max' => 'El documento adicional no debe exceder 5 MB.',
                    ]
                );

                $solicitud = SolicitudDeBeca::where('idSolicitudDeBeca', $id)
                    ->where('idEstudiante', $usuario->estudiante->idEstudiante)
                    ->with('documentaciones')
                    ->firstOrFail();

                if ($solicitud->idEstatus == 6 ) {
                    DB::rollBack();

                    return redirect()
                        ->route('consultaSolicitudBeca')
                        ->with('popupError', 'Tu solicitud está siendo procesada y no puede ser modificada.');
                }

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
            throw $e; 

            return back()
                ->with('popupError', 'Ocurrió un error al actualizar tu solicitud.')
                ->withInput();
        }
    }




}
