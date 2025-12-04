<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.login');
})->name('login');

Route::get('/inicio', function () {
    return view('layouts.inicio');
})->name('inicio');

Route::get('/apartadoUsuarios', function () {
    return view('shared.moduloUsuarios.apartadoUsuarios');
})->name('apartadoUsuarios');

Route::get('/apartadoEstudiantes', function () {
    return view('shared.moduloEstudiantes.apartadoEstudiantes');
})->name('apartadoEstudiantes');

Route::get('/apartadoBecas', function () {
    return view('SGFIDMA.moduloBecas.apartadoBecas');
})->name('apartadoBecas');

Route::get('/apartadoConceptos', function () {
    return view('SGFIDMA.moduloConceptosDePago.apartadoConceptos');
})->name('apartadoConceptos');

Route::get('/apartadoPlanDePago', function () {
    return view('SGFIDMA.moduloPlanDePago.apartadoPlanDePago');
})->name('apartadoPlanDePago');

Route::get('/apartadoSolicitudDeBeca', function () {
    return view('SGFIDMA.moduloSolicitudBeca.apartadoSolicitudBeca');
})->name('apartadoSolicitudBeca');

Route::get('/apartadoPago', function () {
    return view('SGFIDMA.moduloPagos.apartadoPago');
})->name('apartadoPagos');

Route::get('/apartadoReporteFinanzas', function () {
    return view('SGFIDMA.moduloReportesFinanzas.apartadoReportes');
})->name('apartadoReportesFinanzas');
