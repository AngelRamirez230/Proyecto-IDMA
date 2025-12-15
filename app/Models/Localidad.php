<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    protected $table = 'Localidad';
    protected $primaryKey = 'idLocalidad';
    public $timestamps = false;

    protected $fillable = [
        'nombreLocalidad',
        'nombreLocalidadNormalizado',
        'idMunicipio',
        'idTipoDeEstatus',
    ];

    /* ============================
       RELACIONES
    ============================ */

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'idMunicipio', 'idMunicipio');
    }
}
