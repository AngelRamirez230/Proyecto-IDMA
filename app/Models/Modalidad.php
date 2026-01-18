<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modalidad extends Model
{
    protected $table = 'Modalidad';
    protected $primaryKey = 'idModalidad';
    public $timestamps = false;

    protected $fillable = [
        'nombreModalidad',
    ];
}
