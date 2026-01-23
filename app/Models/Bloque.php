<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bloque extends Model
{
    protected $table = 'Bloque';
    protected $primaryKey = 'idBloque';
    public $timestamps = false;

    protected $fillable = [
        'numeroBloque',
        'fechaInicio',
        'fechaFin',
        'idTipoDeEstatus',
        'idCicloModalidad',
    ];
}
