<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelAcademico extends Model
{
    protected $table = 'Nivel_academico';
    protected $primaryKey = 'idNivelAcademico';
    public $timestamps = false;

    protected $fillable = [
        'nombreNivelAcademico',
        'abreviacionNombre',
    ];

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'idNivelAcademico', 'idNivelAcademico');
    }
}
