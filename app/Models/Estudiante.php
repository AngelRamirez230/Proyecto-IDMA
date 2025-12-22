<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'Estudiante';
    protected $primaryKey = 'idEstudiante';
    public $timestamps = false;

    protected $fillable = [
        'idUsuario',
        'matriculaNumerica',
        'matriculaAlfanumerica',
        'grado',
        'creditosAcumulados',
        'promedioGeneral',
        'fechaDeIngreso',
        'idGeneración',
        'idPlanDeEstudios',
        'idTipoDeInscripción',
        'idEstatus',
    ];

    protected $casts = [
        'fechaDeIngreso'     => 'date',
        'creditosAcumulados' => 'integer',
        'promedioGeneral'    => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }

    public function generacion()
    {
        return $this->belongsTo(Generacion::class, 'idGeneracion', 'idGeneracion');
    }

    public function planDeEstudios()
    {
        return $this->belongsTo(PlanDeEstudios::class, 'idPlanDeEstudios', 'idPlanDeEstudios');
    }

    public function tipoDeInscripcion()
    {
        return $this->belongsTo(TipoDeInscripcion::class, 'idTipoDeInscripcion', 'idTipoDeInscripcin');
    }

    public function estatus()
    {
        return $this->belongsTo(TipoDeEstatus::class, 'idEstatus', 'idTipoDeEstatus');
    }
}
