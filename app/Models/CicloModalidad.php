<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CicloModalidad extends Model
{
    protected $table = 'Ciclo_modalidad';
    protected $primaryKey = 'idCicloModalidad';
    public $timestamps = false;

    protected $fillable = [
        'idModalidad',
        'idCicloEscolar',
        'fechaInicio',
        'fechaFin',
        'idTipoDeEstatus',
    ];
}
