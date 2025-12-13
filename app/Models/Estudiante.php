<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'Estudiante';
    protected $primaryKey = 'idEstudiante';
    public $timestamps = false;

    protected $fillable = [
        'matriculaNumerica',
        'matriculaAlfanumerica',
        'idUsuario'
    ];

    /**
     * Relación con Usuario
     * Un estudiante pertenece a un usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }
}
