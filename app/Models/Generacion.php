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
        'idMesInicio',
        'añoDeFinalizacion',
        'idMesFin',
        'nombreGeneracion',
        'claveGeneracion',
        'idEstatus'
    ];

    public function mesInicio()
    {
        return $this->belongsTo(Mes::class, 'idMesInicio', 'idMes');
    }

    public function mesFin()
    {
        return $this->belongsTo(Mes::class, 'idMesFin', 'idMes');
    }

    public function estatus()
    {
        return $this->belongsTo(TipoDeEstatus::class, 'idEstatus', 'idTipoDeEstatus');
    }

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'idGeneracion', 'idGeneracion');
    }
}
