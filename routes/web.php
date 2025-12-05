<?php

use Illuminate\Support\Facades\Route;


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

Route::get('/altaBeca', function () {
    return view('SGFIDMA.moduloBecas.altaDeBeca');
})->name('altaBeca');


/*CONCEPTOS*/
Route::get('/apartadoConceptos', function () {
    return view('SGFIDMA.moduloConceptosDePago.apartadoConceptos');
})->name('apartadoConceptos');

Route::get('/altaConceptos', function () {
    return view('SGFIDMA.moduloConceptosDePago.altaDeConcepto');
})->name('altaConcepto');


/*PLAN DE PAGO*/
Route::get('/apartadoPlanDePago', function () {
    return view('SGFIDMA.moduloPlanDePago.apartadoPlanDePago');
})->name('apartadoPlanDePago');

Route::get('/altaPlanDePago', function () {
    return view('SGFIDMA.moduloPlanDePago.altaPlan');
})->name('altaPlan');


/*SOLICITU DE BECA*/
Route::get('/apartadoSolicitudDeBeca', function () {
    return view('SGFIDMA.moduloSolicitudBeca.apartadoSolicitudBeca');
})->name('apartadoSolicitudBeca');



/*PAGOS*/
Route::get('/apartadoPago', function () {
    return view('SGFIDMA.moduloPagos.apartadoPago');
})->name('apartadoPagos');

/*REPORTES FINANZAS*/
Route::get('/apartadoReporteFinanzas', function () {
    return view('SGFIDMA.moduloReportesFinanzas.apartadoReportesFinanzas');
})->name('apartadoReportesFinanzas');




