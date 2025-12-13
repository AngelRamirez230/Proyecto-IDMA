<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BecaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConceptoController;
use App\Http\Controllers\PlanDePagoController;

/*--------------------------Acceso al sistema--------------------------------*/
Route::get('/login', [LoginController::class, 'showLogin'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');




/*--------------------------SHARED--------------------------------*/
Route::get('/', function () {
    return view('layouts.login');
})->name('login');

Route::get('/inicio', function () {
    return view('layouts.inicio');
})->name('inicio');

/*USUARIOS*/
Route::get('/apartadoUsuarios', function () {
    return view('shared.moduloUsuarios.apartadoUsuarios');
})->name('apartadoUsuarios');

Route::get('/seleccionarRol', function () {
    return view('shared.moduloUsuarios.seleccionarRol');
})->name('seleccionarRol');

/* Nueva ruta: mostrar formulario de creación */
Route::get('/usuarios/crear', [UsuarioController::class, 'create'])
    ->name('usuarios.create');

/* Nueva ruta: guardar usuario */
Route::post('/usuarios', [UsuarioController::class, 'store'])
    ->name('usuarios.store');

/* Ruta antigua → ahora redirige a la nueva */
Route::get('/altaUsuarios', function () {
    return redirect()->route('usuarios.create');
})->name('altaUsuarios');

Route::get('/consultaUsuarios', function () {
    return view('shared.moduloUsuarios.consultaDeUsuarios');
})->name('consultaUsuarios');


/*ESTUDIANTES*/
Route::get('/apartadoEstudiantes', function () {
    return view('shared.moduloEstudiantes.apartadoEstudiantes');
})->name('apartadoEstudiantes');

Route::get('/altaEstudiante', function () {
    return view('shared.moduloEstudiantes.altaEstudiante');
})->name('altaEstudiante');






/*REPORTES*/
Route::get('/apartadoReporte', function () {
    return view('shared.moduloReportes.apartadoReportes');
})->name('apartadoReportes');

Route::get('/apartadoBitacoras', function () {
    return view('shared.moduloReportes.apartadoBitacora');
})->name('apartadoBitacoras');




/*------------------SGFIDMA---------------------------------------*/

/*BECAS*/
Route::get('/apartadoBecas', function () {
    return view('SGFIDMA.moduloBecas.apartadoBecas');
})->name('apartadoBecas');


Route::get('/altaBeca', [BecaController::class, 'create'])->name('altaBeca');

Route::get('/consultaBeca', [BecaController::class, 'index'])->name('consultaBeca');

Route::get('/becas/{id}/modificar', [BecaController::class, 'edit'])->name('becas.edit');

Route::put('/becas/{id}', [BecaController::class, 'update'])->name('becas.update');

Route::post('/becas/store', [BecaController::class, 'store'])->name('becas.store');

Route::delete('/becas/{id}', [BecaController::class, 'destroy'])->name('becas.destroy');




/*CONCEPTOS*/
Route::get('/apartadoConceptos', function () {
    return view('SGFIDMA.moduloConceptosDePago.apartadoConceptos');
})->name('apartadoConceptos');

Route::get('/altaConceptos', [ConceptoController::class, 'create'])->name('altaConcepto');

Route::post('/Conceptos/store', [ConceptoController::class, 'store'])->name('concepto.store');

Route::get('/consultaConceptos', [ConceptoController::class, 'index'])->name('consultaConcepto');

Route::get('/concepto/{idConceptoDePago}/modificar', [ConceptoController::class, 'edit'])->name('concepto.edit');

Route::put('/concepto/{idConceptoDePago}/actualizar', [ConceptoController::class, 'update'])->name('concepto.update');

Route::delete('/concepto/{idConceptoDePago}/eliminar', [ConceptoController::class, 'destroy'])->name('concepto.destroy');



/*PLAN DE PAGO*/
Route::get('/apartadoPlanDePago', function () {
    return view('SGFIDMA.moduloPlanDePago.apartadoPlanDePago');
})->name('apartadoPlanDePago');

Route::get('/altaPlanDePago', [PlanDePagoController::class, 'create'])->name('altaPlan');

Route::post('/altaPlanDePago', [PlanDePagoController::class, 'store'])->name('planes.store');

Route::get('/consultaPlanDePago', [PlanDePagoController::class, 'index'])->name('consultaPlan');

Route::get('/planes/{id}/edit', [PlanDePagoController::class, 'edit'])->name('planes.edit');

Route::put('/planes/{id}', [PlanDePagoController::class, 'update'])->name('planes.update');

Route::delete('/planes/{id}', [PlanDePagoController::class, 'destroy'])->name('planes.destroy');


/*SOLICITU DE BECA*/
Route::get('/apartadoSolicitudDeBeca', function () {
    return view('SGFIDMA.moduloSolicitudBeca.apartadoSolicitudBeca');
})->name('apartadoSolicitudBeca');


Route::get('/formularioSolicitudDeBeca', function () {
    return view('SGFIDMA.moduloSolicitudBeca.formularioSolicitudDeBeca');
})->name('formularioSolicitudBeca');



/*PAGOS*/
Route::get('/apartadoPago', function () {
    return view('SGFIDMA.moduloPagos.apartadoPago');
})->name('apartadoPagos');

Route::get('/consultaPagos', function () {
    return view('SGFIDMA.moduloPagos.consultaDePagos');
})->name('consultaPagos');

Route::get('/detallesPago', function () {
    return view('SGFIDMA.moduloPagos.detallesDePago');
})->name('detallesPago');


/*REPORTES FINANZAS*/
Route::get('/apartadoReporteFinanzas', function () {
    return view('SGFIDMA.moduloReportesFinanzas.apartadoReportesFinanzas');
})->name('apartadoReportesFinanzas');

Route::get('/eleccionFechas', function () {
    return view('SGFIDMA.moduloReportesFinanzas.eleccionDeFechas');
})->name('eleccionFechas');

Route::get('/reportePagosAprobados', function () {
    return view('SGFIDMA.moduloReportesFinanzas.reportePagosAprobados');
})->name('reportePagosAprobados');

