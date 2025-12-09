<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDeUnidad extends Model
{
    protected $table = 'tipo_de_unidad';
    protected $primaryKey = 'idTipoDeUnidad';

    public $timestamps = false;

    protected $fillable = [
        'nombreUnidad'
    ];
}
