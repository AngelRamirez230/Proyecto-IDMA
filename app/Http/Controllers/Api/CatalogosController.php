<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use App\Models\Localidad;

class CatalogosController extends Controller
{
    /**
     * Devuelve municipios activos (idTipoDeEstatus = 1)
     * filtrados por entidad
     */
    public function municipios($idEntidad)
    {
        $municipios = Municipio::where('idEntidad', $idEntidad)
            ->where('idTipoDeEstatus', 1) // SOLO ACTIVOS
            ->orderBy('nombreMunicipio', 'ASC')
            ->get([
                'idMunicipio',
                'nombreMunicipio'
            ]);

        return response()->json($municipios);
    }

    /**
     * Devuelve localidades activas (idTipoDeEstatus = 1)
     * filtradas por municipio
     */
    public function localidades($idMunicipio)
    {
        $localidades = Localidad::where('idMunicipio', $idMunicipio)
            ->where('idTipoDeEstatus', 1) // SOLO ACTIVAS
            ->orderBy('nombreLocalidad', 'ASC')
            ->get([
                'idLocalidad',
                'nombreLocalidad'
            ]);

        return response()->json($localidades);
    }
}
