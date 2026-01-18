<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoEstudiante extends Model
{
    protected $table = 'Grupo_estudiante';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'idGrupo',
        'idEstudiante',
    ];
}
