<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sexo extends Model
{
    protected $table = 'Sexo';
    protected $primaryKey = 'idSexo';
    public $timestamps = false;

    protected $fillable = [
        'nombreSexo',
    ];

    // Un sexo puede estar asociado a muchos usuarios
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idSexo', 'idSexo');
    }
}
