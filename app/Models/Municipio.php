<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'Municipio';
    protected $primaryKey = 'idMunicipio';
    public $timestamps = false;

    protected $fillable = [
        'nombreMunicipio',
        'idEntidad',
        'otroMunicipio',
    ];

    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'idEntidad', 'idEntidad');
    }

    public function localidades()
    {
        return $this->hasMany(Localidad::class, 'idMunicipio', 'idMunicipio');
    }
}
