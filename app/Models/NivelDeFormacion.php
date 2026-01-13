<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelDeFormacion extends Model
{
    protected $table = 'Nivel_de_formacion';
    protected $primaryKey = 'idNivel_de_formacion';
    public $timestamps = false;

    protected $fillable = [
        'nombreNivel',
    ];
}
