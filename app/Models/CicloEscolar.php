<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CicloEscolar extends Model
{
    protected $table = 'Ciclo_escolar';
    protected $primaryKey = 'idCicloEscolar';
    public $timestamps = false;

    protected $fillable = [
        'nombreCicloEscolar',
    ];
}
