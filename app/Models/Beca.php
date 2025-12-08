<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TipoDeEstatus; // IMPORTANTE: importar la clase

class Beca extends Model
{
    protected $table = 'beca'; // Nombre exacto de tu tabla
    protected $primaryKey = 'idBeca';
    public $timestamps = false;

    protected $fillable = [
        'nombreDeBeca',
        'porcentajeDeDescuento',
        'idEstatus'
    ];

    // Relación con TipoDeEstatus
    public function estatus()
    {
        return $this->belongsTo(TipoDeEstatus::class, 'idEstatus', 'idTipoDeEstatus');
    }
}
