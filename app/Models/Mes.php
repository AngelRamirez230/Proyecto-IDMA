<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mes extends Model
{
    protected $table = 'Mes';
    protected $primaryKey = 'idMes';
    public $timestamps = false;

    protected $fillable = [
        'nombreMes',
        'nombreCorto'
    ];

    
    public function generacionesInicio()
    {
        return $this->hasMany(Generacion::class, 'idMesInicio', 'idMes');
    }


    public function generacionesFin()
    {
        return $this->hasMany(Generacion::class, 'idMesFin', 'idMes');
    }
}
