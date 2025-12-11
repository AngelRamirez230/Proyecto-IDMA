<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    protected $table = 'Entidad';
    protected $primaryKey = 'idEntidad';
    public $timestamps = false;

    protected $fillable = [
        'nombreEntidad',
    ];

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'idEntidad', 'idEntidad');
    }
}
