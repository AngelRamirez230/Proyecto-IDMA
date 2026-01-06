<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'Empleado';
    protected $primaryKey = 'idEmpleado';
    public $timestamps = false;

    protected $fillable = [
        'idUsuario',
        'idDepartamento',
        'idNivelAcademico',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'idDepartamento', 'idDepartamento');
    }

    public function nivelAcademico()
    {
        return $this->belongsTo(NivelAcademico::class, 'idNivelAcademico', 'idNivelAcademico');
    }
}
