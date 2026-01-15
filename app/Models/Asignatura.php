<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    protected $table = 'Asignatura';
    protected $primaryKey = 'idAsignatura';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
