<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'Pais';
    protected $primaryKey = 'idPais';
    public $timestamps = false;

    public function entidades()
    {
        return $this->hasMany(Entidad::class, 'idPais', 'idPais');
    }
}
