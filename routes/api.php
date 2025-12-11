<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CatalogosController;

Route::get('/municipios/{idEntidad}', [CatalogosController::class, 'municipios']);
Route::get('/localidades/{idMunicipio}', [CatalogosController::class, 'localidades']);
