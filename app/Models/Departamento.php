<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'Departamento';
    protected $primaryKey = 'idDepartamento';
    public $timestamps = false;

    protected $fillable = [
        'nombreDepartamento',
    ];

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'idDepartamento', 'idDepartamento');
    }
}
