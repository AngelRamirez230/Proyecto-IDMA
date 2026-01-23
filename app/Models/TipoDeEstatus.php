<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDeEstatus extends Model
{
    protected $table = 'tipo_de_estatus';
    protected $primaryKey = 'idTipoDeEstatus';
    public $timestamps = false;

    protected $fillable = [
        'nombreTipoDeEstatus'
    ];

    public function estudiantePlanes()
    {
        return $this->hasMany(
            EstudiantePlan::class,
            'idEstatus',
            'idTipoDeEstatus'
        );
    }
}
