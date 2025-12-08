<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beca extends Model
{
    protected $table = 'beca'; // Nombre exacto de tu tabla
    protected $primaryKey = 'idBeca';
    public $timestamps = false;  

    protected $fillable = [
        'nombreDeBeca',
        'porcentajeDeDescuento',
        'idEstatus'
    ];

}
