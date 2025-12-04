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

/*ESTUDIANTES*/
Route::get('/apartadoEstudiantes', function () {
    return view('shared.moduloEstudiantes.apartadoEstudiantes');
})->name('apartadoEstudiantes');


/*REPORTES*/
Route::get('/apartadoReporte', function () {
    return view('shared.moduloReportes.apartadoReportes');
})->name('apartadoReportes');




/*------------------SGFIDMA---------------------------------------*/

/*BECAS*/
Route::get('/apartadoBecas', function () {
    return view('SGFIDMA.moduloBecas.apartadoBecas');
})->name('apartadoBecas');


/*CONCEPTOS*/
Route::get('/apartadoConceptos', function () {
    return view('SGFIDMA.moduloConceptosDePago.apartadoConceptos');
})->name('apartadoConceptos');


/*PLAN DE PAGO*/
Route::get('/apartadoPlanDePago', function () {
    return view('SGFIDMA.moduloPlanDePago.apartadoPlanDePago');
})->name('apartadoPlanDePago');


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




