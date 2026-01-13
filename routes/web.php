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
use App\Models\Empleado;
use App\Http\Controllers\PagoController;



/*--------------------------RUTAS PARA INVITADOS (LOGIN)--------------------------*/
Route::middleware(['guest.manual', 'nocache'])->group(function () {
    

    Route::get('/', [LoginController::class, 'showLogin'])->name('login.view');


    Route::get('/login', [LoginController::class, 'showLogin'])->name('login.form');

    
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});

/*--------------------------RUTAS PROTEGIDAS (USUARIOS AUTENTICADOS)--------------------------*/
Route::middleware(['auth.manual', 'nocache', 'activity.timeout'])->group(function () {


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
    Route::get('/{id}/editar', [EstudianteController::class, 'edit'])->name('estudiantes.edit');
    Route::put('/{id}', [EstudianteController::class, 'update'])->name('estudiantes.update');
    Route::delete('/{id}', [EstudianteController::class, 'destroy'])->name('estudiantes.destroy');
    
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

    Route::get('/apartadoBitacoras', function () {
        return view('shared.moduloReportes.apartadoBitacora');
    })->name('apartadoBitacoras');

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

    Route::get('/consultaPagos', function () {
        return view('SGFIDMA.moduloPagos.consultaDePagos');
    })->name('consultaPagos');

    Route::get('/detallesPago', function () {
        return view('SGFIDMA.moduloPagos.detallesDePago');
    })->name('detallesPago');

    /*----------- REPORTES FINANZAS -----------*/
    Route::get('/apartadoReporteFinanzas', function () {
        return view('SGFIDMA.moduloReportesFinanzas.apartadoReportesFinanzas');
    })->name('apartadoReportesFinanzas');

    Route::get('/eleccionFechas', function () {
        return view('SGFIDMA.moduloReportesFinanzas.eleccionDeFechas');
    })->name('eleccionFechas');

    Route::get('/reportePagosAprobados', function () {
        return view('SGFIDMA.moduloReportesFinanzas.reportePagosAprobados');
    })->name('reportePagosAprobados');

});
