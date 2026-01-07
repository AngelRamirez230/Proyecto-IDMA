<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaSemana extends Model
{
    protected $table = 'Dia_semana';
    protected $primaryKey = 'idDiaSemana';
    public $timestamps = false;

    protected $fillable = [
        'nombreDia',
    ];
}
