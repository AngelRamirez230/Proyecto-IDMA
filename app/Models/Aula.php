<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $table = 'Aula';
    protected $primaryKey = 'idAula';
    public $timestamps = false;

    protected $fillable = [
        'nombreAula',
    ];
}
