<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domicilio extends Model
{
    protected $table = 'Domicilio';
    protected $primaryKey = 'idDomicilio';
    public $timestamps = false;

    protected $fillable = [
        'codigoPostal',
        'calle',
        'numeroExterior',
        'numeroInterior',
        'colonia',
        'idLocalidad'
    ];

    public function localidad()
    {
        return $this->belongsTo(Localidad::class, 'idLocalidad', 'idLocalidad');
    }
}