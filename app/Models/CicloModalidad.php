<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CicloModalidad extends Model
{
    protected $table = 'Ciclo_modalidad';
    protected $primaryKey = 'idCicloModalidad';
    public $timestamps = false;

    protected $fillable = [
        'idModalidad',
        'idCicloEscolar',
        'idLicenciatura',
        'fechaInicio',
        'fechaFin',
        'idTipoDeEstatus',
    ];


    public function cicloEscolar()
    {
        return $this->belongsTo(
            CicloEscolar::class,
            'idCicloEscolar',
            'idCicloEscolar'
        );
    }
}
