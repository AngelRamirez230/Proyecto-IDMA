<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstudiantePlan extends Model
{
    protected $table = 'Estudiante_plan';

    protected $primaryKey = 'idEstudiantePlan';

    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'idEstudiante',
        'idPlanDePago',
        'idEstatus',
        'fechaDeAsignacion',
        'fechaDeFinalizacion',
    ];

    protected $casts = [
        'fechaDeAsignacion'   => 'date',
        'fechaDeFinalizacion' => 'date',
    ];

    // =========================
    // RELACIONES
    // =========================

    public function estudiante()
    {
        return $this->belongsTo(
            Estudiante::class,
            'idEstudiante',
            'idEstudiante'
        );
    }

    public function planDePago()
    {
        return $this->belongsTo(
            PlanDePago::class,
            'idPlanDePago',
            'idPlanDePago'
        );
    }

    public function estatus()
    {
        return $this->belongsTo(
            TipoDeEstatus::class,
            'idEstatus',
            'idTipoDeEstatus'
        );
    }
}
