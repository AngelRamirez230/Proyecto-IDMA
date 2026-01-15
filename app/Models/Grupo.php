<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'Grupo';
    protected $primaryKey = 'idGrupo';
    public $timestamps = false;

    protected $fillable = [
        'nombreGrupo',
        'claveGrupo',
        'semestre',
        'periodoAcademico',
        'idModalidad',
        'idLicenciatura',
        'idEstatus',
    ];
}
