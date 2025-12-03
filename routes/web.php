<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/inicio', function () {
    return view('inicio');
});

Route::get('/apartadoUsuarios', function () {
    return view('moduloUsuarios.apartadoUsuarios');
});

Route::get('/apartadoEstudiantes', function () {
    return view('moduloEstudiantes.apartadoEstudiantes');
});

Route::get('/apartadoBecas', function () {
    return view('moduloBecas.apartadoBecas');
});

Route::get('/apartadoConceptos', function () {
    return view('moduloConceptosDePago.apartadoConceptos');
});

Route::get('/apartadoPlanDePago', function () {
    return view('moduloPlanDePago.apartadoPlanDePago');
});

Route::get('/apartadoSolicitudDeBeca', function () {
    return view('moduloSolicitudBeca.apartadoSolicitudBeca');
});

Route::get('/apartadoPago', function () {
    return view('moduloPagos.apartadoPago');
});

Route::get('/apartadoReporteFinanzas', function () {
    return view('moduloReportesFinanzas.apartadoReportes');
});