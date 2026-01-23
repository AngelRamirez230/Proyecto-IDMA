<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'Horario';
    protected $primaryKey = 'idHorario';
    public $timestamps = false;

    protected $fillable = [
        'idAsignatura',
        'idGrupo',
        'idDocente',
        'idAula',
        'idBloque',
    ];
}
