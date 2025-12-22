<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licenciatura extends Model
{
    protected $table = 'Licenciatura';
    protected $primaryKey = 'idLicenciatura';
    public $timestamps = false;

    protected $fillable = [
        'nombreLicenciatura'
    ];

    public function planesDeEstudio()
    {
        return $this->hasMany(PlanDeEstudios::class, 'idLicenciatura', 'idLicenciatura');
    }
}
