<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TipoDeEstatus;

class PlanDePago extends Model
{
    protected $table = 'plan_de_pago';
    protected $primaryKey = 'idPlanDePago';
    public $timestamps = false;

    protected $fillable = [
        'nombrePlanDePago',
        'idEstatus'
    ];

    // Relación con los conceptos asignados
    public function conceptos()
    {
        return $this->hasMany(PlanConcepto::class, 'idPlanDePago', 'idPlanDePago')
            ->with('concepto');
    }

    public function estatus()
    {
        return $this->belongsTo(TipoDeEstatus::class, 'idEstatus', 'idTipoDeEstatus');
    }


    public function estudiantes()
    {
        return $this->hasMany(
            EstudiantePlan::class,
            'idPlanDePago',
            'idPlanDePago'
        );
    }
}
