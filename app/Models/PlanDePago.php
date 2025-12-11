<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(PlanConcepto::class, 'idPlanDePago', 'idPlanDePago');
    }
}
