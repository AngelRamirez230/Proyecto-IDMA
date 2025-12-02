<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/inicio', function () {
    return view('inicio');
});

Route::get('/apartadoUsuarios', function () {
    return view('moduloUsuario.apartadoUsuarios');
});
