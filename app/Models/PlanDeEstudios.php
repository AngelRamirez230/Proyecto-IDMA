<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanDeEstudios extends Model
{
    protected $table = 'Plan_de_estudios';
    protected $primaryKey = 'idPlanDeEstudios';
    public $timestamps = false;

    protected $fillable = [
        'nombrePlanDeEstudios',
        'documentoPlanDeEstudios',
        'idLicenciatura'
    ];

    public function licenciatura()
    {
        return $this->belongsTo(Licenciatura::class, 'idLicenciatura', 'idLicenciatura');
    }

}
