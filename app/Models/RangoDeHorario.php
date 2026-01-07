<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RangoDeHorario extends Model
{
    protected $table = 'Rango_de_horario';
    protected $primaryKey = 'idRangoDeHorario';
    public $timestamps = false;

    protected $fillable = [
        'horaInicio',
        'horaFin',
        'idTipoDeEstatus',
    ];
}
