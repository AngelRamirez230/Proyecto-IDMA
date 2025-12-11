<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    protected $table = 'Localidad';
    protected $primaryKey = 'idLocalidad';
    public $timestamps = false;

    protected $fillable = [
        'nombreLocalidad',
        'idMunicipio',
        'otraLocalidad',
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'idMunicipio', 'idMunicipio');
    }
}
