<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Generacion extends Model
{
    protected $table = 'Generacion';
    protected $primaryKey = 'idGeneracion';


    public $timestamps = false;

    protected $fillable = [
        'añoDeInicio',
        'idMes',
        'idEstatus'
    ];

    public function mes()
    {
        return $this->belongsTo(Mes::class, 'idMes', 'idMes');
    }

    public function estatus()
    {
        return $this->belongsTo(Tipo_de_estatus::class, 'idEstatus', 'idTipoDeEstatus');
    }

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'idGeneracion', 'idGeneracion');
    }
}
