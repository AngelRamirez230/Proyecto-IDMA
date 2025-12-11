<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use App\Models\Localidad;

class CatalogosController extends Controller
{
    public function municipios($idEntidad)
    {
        $municipios = Municipio::where('idEntidad', $idEntidad)
            ->orderBy('nombreMunicipio', 'ASC')
            ->get(['idMunicipio', 'nombreMunicipio']);

        return response()->json($municipios);
    }

    public function localidades($idMunicipio)
    {
        $localidades = Localidad::where('idMunicipio', $idMunicipio)
            ->orderBy('nombreLocalidad', 'ASC')
            ->get(['idLocalidad', 'nombreLocalidad']);

        return response()->json($localidades);
    }
}
