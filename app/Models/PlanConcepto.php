<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanConcepto extends Model
{
    protected $table = 'plan_conceptos';
    public $timestamps = false;

    protected $fillable = [
        'idPlanDePago',
        'idConceptoDePago',
        'cantidad'
    ];

    public function plan()
    {
        return $this->belongsTo(PlanDePago::class, 'idPlanDePago', 'idPlanDePago');
    }

    public function concepto()
    {
        return $this->belongsTo(\App\Models\ConceptoDePago::class, 'idConceptoDePago', 'idConceptoDePago');
    }
}
