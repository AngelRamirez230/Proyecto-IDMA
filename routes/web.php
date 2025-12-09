<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BecaController;




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

Route::get('/altaUsuarios', function () {
    return view('shared.moduloUsuarios.altaDeUsuario');
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

Route::get('/altaConceptos', function () {
    return view('SGFIDMA.moduloConceptosDePago.altaDeConcepto');
})->name('altaConcepto');

Route::get('/consultaConceptos', function () {
    return view('SGFIDMA.moduloConceptosDePago.consultaDeConceptos');
})->name('consultaConceptos');

Route::get('/modificacionConceptos', function () {
    return view('SGFIDMA.moduloConceptosDePago.modificacionConcepto');
})->name('modificacionConceptos');



/*PLAN DE PAGO*/
Route::get('/apartadoPlanDePago', function () {
    return view('SGFIDMA.moduloPlanDePago.apartadoPlanDePago');
})->name('apartadoPlanDePago');

Route::get('/altaPlanDePago', function () {
    return view('SGFIDMA.moduloPlanDePago.altaPlan');
})->name('altaPlan');

Route::get('/consultaPlanDePago', function () {
    return view('SGFIDMA.moduloPlanDePago.consultaPlanDePago');
})->name('consultaPlan');


/*SOLICITU DE BECA*/
Route::get('/apartadoSolicitudDeBeca', function () {
    return view('SGFIDMA.moduloSolicitudBeca.apartadoSolicitudBeca');
})->name('apartadoSolicitudBeca');



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

Route::get('/reportePagosAprobados', function () {
    return view('SGFIDMA.moduloReportesFinanzas.reportePagosAprobados');
})->name('reportePagosAprobados');


