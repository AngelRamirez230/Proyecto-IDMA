<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BecaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConceptoController;
use App\Http\Controllers\PlanDePagoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\GeneracionController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\SolicitudDeBecaController;
use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\GrupoController;
use App\Models\Empleado;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\PagoEstudianteController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CalificacionesController;
use App\Http\Controllers\ReporteFinancieroController;
use App\Http\Controllers\EstadoDeCuentaController;


/*--------------------------RUTAS PARA INVITADOS (LOGIN)--------------------------*/
Route::middleware(['guest.manual', 'nocache'])->group(function () {
    

    Route::get('/', [LoginController::class, 'showLogin'])->name('login.view');


    Route::get('/login', [LoginController::class, 'showLogin'])->name('login.form');

    
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});

/*--------------------------RUTAS PROTEGIDAS (USUARIOS AUTENTICADOS)--------------------------*/
Route::middleware(['auth.manual', 'nocache', 'activity.timeout', 'bitacora'])->group(function () {


    /*------------INICIO------------*/
    Route::get('/inicio', [InicioController::class, 'index'])->name('inicio');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/prueba-middleware', function () {
        return 'Acceso autorizado: sesión activa';
    });


    /*----------- USUARIOS -----------*/
    Route::get('/apartadoUsuarios', function () {
        return view('shared.moduloUsuarios.apartadoUsuarios');
    })->name('apartadoUsuarios');

    Route::get('/seleccionarRol', function () {
        return view('shared.moduloUsuarios.seleccionarRol');
    })->name('seleccionarRol');

    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');

    Route::get('/altaUsuarios', function () {
        return redirect()->route('usuarios.create');
    })->name('altaUsuarios');

    Route::get('/consultaUsuarios', [UsuarioController::class, 'consultaUsuarios'])
    ->name('consultaUsuarios');

    Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show'])
    ->name('usuarios.show');

    Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');

    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');

    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])
    ->name('usuarios.destroy');

    Route::put('/usuarios/{usuario}/toggle-estatus', [UsuarioController::class, 'toggleEstatus'])
    ->name('usuarios.toggleEstatus');

    /*----------- ESTUDIANTES -----------*/
    Route::get('/apartadoEstudiantes', function () {
        return view('shared.moduloEstudiantes.apartadoEstudiantes');
    })->name('apartadoEstudiantes');

    Route::get('/altaEstudiante', [EstudianteController::class, 'create'])->name('altaEstudiante');
    Route::post('/estudiantes/store', [EstudianteController::class, 'store'])->name('estudiantes.store');
    Route::post('/generaciones/crear-dashboard',[GeneracionController::class, 'crearDesdeDashboard'])->name('generaciones.crearDashboard');
    Route::get('/consultaEstudiantes', [EstudianteController::class, 'index'])->name('consultaEstudiantes');
    Route::get('/estudiantes', [EstudianteController::class, 'index'])->name('estudiantes.index');
    Route::get('/estudiantes/{id}/editar', [EstudianteController::class, 'edit'])
        ->name('estudiantes.edit');
    Route::put('/estudiantes/{id}', [EstudianteController::class, 'update'])
        ->name('estudiantes.update');
    Route::delete('/estudiantes/{id}', [EstudianteController::class, 'destroy'])
        ->name('estudiantes.destroy');
    
    /*----------- ASIGNATURAS -----------*/
    Route::get('/apartadoAsignaturas', function () {
        $usuario = Auth::user();
        $puedeAlta = false;

        if ($usuario && (int) $usuario->idtipoDeUsuario === 1) {
            $puedeAlta = true;
        } else {
            $empleado = $usuario ? Empleado::where('idUsuario', $usuario->idUsuario)->first() : null;
            $departamentosAutorizados = [2, 4, 5, 7];
            $puedeAlta = $empleado
                && in_array((int) $empleado->idDepartamento, $departamentosAutorizados, true);
        }

        return view('SGAIDMA.moduloAsignaturas.apartadoAsignaturas', compact('puedeAlta'));
    })->name('apartadoAsignaturas');

    Route::get('/altaAsignatura', [AsignaturaController::class, 'create'])->name('altaAsignatura');
    Route::post('/asignaturas', [AsignaturaController::class, 'store'])->name('asignaturas.store');

    Route::get('/consultaAsignatura', [AsignaturaController::class, 'index'])
        ->name('consultaAsignatura');
    Route::get('/asignaturas/{id}', [AsignaturaController::class, 'show'])
        ->name('asignaturas.show');
    Route::get('/asignaturas/{id}/edit', [AsignaturaController::class, 'edit'])
        ->name('asignaturas.edit');
    Route::put('/asignaturas/{id}', [AsignaturaController::class, 'update'])
        ->name('asignaturas.update');
    Route::delete('/asignaturas/{id}', [AsignaturaController::class, 'destroy'])
        ->name('asignaturas.destroy');


    /*----------- REPORTES -----------*/
    Route::get('/apartadoReporte', function () {
        return view('shared.moduloReportes.apartadoReportes');
    })->name('apartadoReportes');

    /*----------- DOCENTES -----------*/
    Route::get('/apartadoDocentes', function () {
        return view('SGAIDMA.moduloDocentes.apartadoDocentes');
    })->name('apartadoDocentes');

    /*----------- CALIFICACIONES -----------*/
    Route::get('/apartadoCalificaciones', function () {
        return view('SGAIDMA.moduloCalificaciones.apartadoCalificaciones');
    })->name('apartadoCalificaciones');

    Route::get('/apartadoBitacoras', function () {
        return view('shared.moduloReportes.apartadoBitacora');
    })->name('apartadoBitacoras');
    Route::get('/consultaBitacoras', [BitacoraController::class, 'index'])->name('consultaBitacoras');

    /*----------- BECAS -----------*/
    Route::get('/apartadoBecas', function () {
        return view('SGFIDMA.moduloBecas.apartadoBecas');
    })->name('apartadoBecas');

    Route::get('/altaBeca', [BecaController::class, 'create'])->name('altaBeca');
    Route::post('/becas/store', [BecaController::class, 'store'])->name('becas.store');
    Route::get('/consultaBeca', [BecaController::class, 'index'])->name('consultaBeca');
    Route::get('/becas/{id}/modificar', [BecaController::class, 'edit'])->name('becas.edit');
    Route::put('/becas/{id}', [BecaController::class, 'update'])->name('becas.update');
    Route::delete('/becas/{id}', [BecaController::class, 'destroy'])->name('becas.destroy');

    /*----------- CONCEPTOS -----------*/
    Route::get('/apartadoConceptos', function () {
        return view('SGFIDMA.moduloConceptosDePago.apartadoConceptos');
    })->name('apartadoConceptos');

    Route::get('/altaConceptos', [ConceptoController::class, 'create'])->name('altaConcepto');
    Route::post('/Conceptos/store', [ConceptoController::class, 'store'])->name('concepto.store');
    Route::get('/consultaConceptos', [ConceptoController::class, 'index'])->name('consultaConcepto');
    Route::get('/concepto/{idConceptoDePago}/modificar', [ConceptoController::class, 'edit'])->name('concepto.edit');
    Route::put('/concepto/{idConceptoDePago}/actualizar', [ConceptoController::class, 'update'])->name('concepto.update');
    Route::delete('/concepto/{idConceptoDePago}/eliminar', [ConceptoController::class, 'destroy'])->name('concepto.destroy');

    /*----------- PLAN DE PAGO -----------*/
    Route::get('/apartadoPlanDePago', function () {
        return view('SGFIDMA.moduloPlanDePago.apartadoPlanDePago');
    })->name('apartadoPlanDePago');

    Route::get('/altaPlanDePago', [PlanDePagoController::class, 'create'])->name('altaPlan');
    Route::post('/altaPlanDePago', [PlanDePagoController::class, 'store'])->name('planes.store');
    Route::get('/consultaPlanDePago', [PlanDePagoController::class, 'index'])->name('consultaPlan');
    Route::get('/planes/{id}/edit', [PlanDePagoController::class, 'edit'])->name('planes.edit');
    Route::put('/planes/{id}', [PlanDePagoController::class, 'update'])->name('planes.update');
    Route::delete('/planes/{id}', [PlanDePagoController::class, 'destroy'])->name('planes.destroy');

    Route::get('/admin/plan-pago/asignar',[PlanDePagoController::class, 'asignarCreate'])->name('admin.planPago.asignar.create');
    Route::post('/admin/plan-pago/asignar',[PlanDePagoController::class, 'asignarStore'])->name('admin.planPago.asignar.store');
    Route::get('/admin/plan-pago/detalles-asignacion',[PlanDePagoController::class, 'detallesAsignacionDePlan'])->name('planPago.detallesAsignacion');

    /*----------- SOLICITUD DE BECA -----------*/

    Route::get('/consulta-solicitudes-beca',[SolicitudDeBecaController::class, 'index'])->name('consultaSolicitudBeca');
    // formulario
    Route::get('/solicitud-beca/crear/{idBeca}',[SolicitudDeBecaController::class, 'create'])->name('solicitud-beca.create');
    // guardar
    Route::post('/solicitud-beca',[SolicitudDeBecaController::class, 'store'])->name('solicitud-beca.store');
    // ver documento
    Route::get('/solicitud-beca/documento/{id}',[SolicitudDeBecaController::class, 'verDocumento'])->name('solicitud-beca.documento');
    // editar solicitud
    Route::get('/solicitud-beca/{id}/editar',[SolicitudDeBecaController::class, 'edit'])->name('solicitud-beca.edit');

    // actualizar solicitud
    Route::put('/solicitud-beca/{id}',[SolicitudDeBecaController::class, 'update'])->name('solicitud-beca.update');



    /*----------- PAGOS -----------*/

    
    Route::get('/pago/generar-referencia/{idConcepto}',[PagoController::class, 'generarReferencia'])->name('pago.generar-referencia');

    Route::get('/apartadoPago', function () {
        return view('SGFIDMA.moduloPagos.apartadoPago');
    })->name('apartadoPagos');

    Route::get('/consultaPagos',[PagoController::class, 'index'])->name('consultaPagos');
    Route::get('/pagos/eliminar', [PagoController::class, 'vistaEliminar'])->name('pagos.eliminar.vista');
    Route::get('/pagos/{referencia}',[PagoController::class, 'show'])->name('pagos.show');
    Route::get('/pagos/{referencia}/recibo',[PagoController::class, 'descargarRecibo'])->name('pagos.recibo');
    Route::delete('/pagos/{referencia}', [PagoController::class, 'destroy'])->name('pagos.destroy');
    
    

    // VALIDACION DE PAGOS
    Route::get('/validar/pagos', [PagoController::class, 'vistaValidarPagos'])->name('pagos.validar');
    Route::post('/validar/pagos/archivo', [PagoController::class, 'validarArchivo'])->name('pagos.validarArchivo');
    Route::post('/pagos/validar/{referencia}', [PagoController::class, 'validarPago'])->name('pagos.validarPago');




    /*----------- GENERACION DE PAGOS DESDE ADMINISTRADOR -----------*/

    Route::get('/admin/pagos/asignar',[PagoEstudianteController::class, 'create'])->name('admin.pagos.create');
    Route::post('/admin/pagos/asignar',[PagoEstudianteController::class, 'store'])->name('admin.pagos.store');
    Route::get('/admin/pagos/detalles-referencias',[PagoEstudianteController::class, 'detallesReferencias'])->name('pagos.detalles-referencias');
    Route::post('/admin/pagos/ciclos-por-estudiantes', [PagoController::class, 'obtenerCiclosPorEstudiantes'])->name('admin.pagos.ciclosPorEstudiantes');
    Route::post('/admin/pagos/referencias-vencidas', [PagoController::class, 'referenciasVencidas'])->name('admin.pagos.referenciasVencidas');

    /*----------- NOTIFICACIONES -----------*/

    Route::post('/notificaciones/{id}/leida', [NotificacionController::class, 'marcarComoLeida'])->name('notificaciones.leida');


    /*----------- REPORTES FINANZAS -----------*/
    Route::get('/apartadoReporteFinanzas', function () {return view('SGFIDMA.moduloReportesFinanzas.apartadoReportesFinanzas');})->name('apartadoReportesFinanzas');
    Route::get('/eleccionFechas/{tipo}', [ReporteFinancieroController::class, 'fechas'])->name('eleccionFechas');
    Route::post('/reportes/vista-previa', [ReporteFinancieroController::class, 'vistaPrevia'])->name('reportes.vistaPrevia');
    Route::post('/reportes/exportar-pdf', [ReporteFinancieroController::class, 'exportarPDF'])->name('reportes.pdf');
    Route::post('/reportes/exportar-excel', [ReporteFinancieroController::class, 'exportarExcel'])->name('reportes.excel');


    Route::get('/reportes/kardex/seleccionar-estudiante',[ReporteFinancieroController::class, 'seleccionarEstudianteKardex'])->name('kardex.seleccionar.estudiante');


    /*----------- ESTADOS DE CUENTA -----------*/
    Route::get('/apartadoEstadosDeCuenta', function () {
        return view('SGFIDMA.moduloEstadoDeCuenta.apartadoEstadoDeCuenta');
    })->name('apartadoEstadoDeCuenta');

    Route::get('/estados-de-cuenta/seleccionar-estudiante',[EstadoDeCuentaController::class, 'seleccionarEstudiante'])->name('estadosCuenta.seleccionarEstudiante');
    Route::post('/estado-de-cuenta/vista-previa',[EstadoDeCuentaController::class, 'vistaPreviaEstadoDeCuenta'])->name('estadoCuenta.vistaPrevia');
    Route::get('/estado-de-cuenta/mi-estado',[EstadoDeCuentaController::class, 'miEstadoDeCuenta'])->name('estadoCuenta.miEstado');



    /*----------- GRUPOS -----------*/
    Route::get('/apartadoGrupos', function () {
        $usuario = Auth::user();

        if (!$usuario || !in_array((int) $usuario->idtipoDeUsuario, [1, 2], true)) {
            abort(403, 'No autorizado');
        }

        return view('SGAIDMA.moduloGrupos.apartadoGrupos');
    })->name('apartadoGrupos');

    Route::get('/altaGrupo', [GrupoController::class, 'create'])->name('altaGrupo');
    Route::post('/grupos', [GrupoController::class, 'store'])->name('grupos.store');
    Route::get('/consultaGrupo', [GrupoController::class, 'index'])->name('consultaGrupo');
    Route::get('/grupos/{id}', [GrupoController::class, 'show'])->name('grupos.show');
    Route::get('/grupos/{id}/edit', [GrupoController::class, 'edit'])->name('grupos.edit');
    Route::put('/grupos/{id}', [GrupoController::class, 'update'])->name('grupos.update');
    Route::put('/grupos/{id}/toggle-estatus', [GrupoController::class, 'toggleEstatus'])
        ->name('grupos.toggleEstatus');
    Route::post('/grupos/{id}/asignar-estudiantes', [GrupoController::class, 'asignarEstudiantes'])
        ->name('grupos.asignarEstudiantes');
    Route::post('/grupos/{id}/desasignar-estudiantes', [GrupoController::class, 'desasignarEstudiantes'])
        ->name('grupos.desasignarEstudiantes');
    Route::delete('/grupos/{id}', [GrupoController::class, 'destroy'])->name('grupos.destroy');

    /*----------- HORARIOS -----------*/
    Route::get('/apartadoHorarios', [HorarioController::class, 'apartado'])->name('apartadoHorarios');
    Route::get('/altaHorario', [HorarioController::class, 'create'])->name('altaHorario');
    Route::post('/horarios', [HorarioController::class, 'store'])->name('horarios.store');
    Route::get('/consultaHorarios', [HorarioController::class, 'index'])->name('consultaHorarios');
    Route::delete('/horarios/{id}', [HorarioController::class, 'destroy'])->name('horarios.destroy');

    /*----------- CALIFICACIONES -----------*/
    Route::get('/consultaCalificaciones', [CalificacionesController::class, 'index'])->name('consultaCalificaciones');
    Route::get('/calificaciones/{id}/editar', [CalificacionesController::class, 'edit'])->name('calificaciones.edit');

});
