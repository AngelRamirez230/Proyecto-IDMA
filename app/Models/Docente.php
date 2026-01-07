<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'Docente';
    protected $primaryKey = 'idDocente';
    public $timestamps = false;

    protected $fillable = [
        'idUsuario',
        'idNivelAcademico',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }

    public function nivelAcademico()
    {
        return $this->belongsTo(NivelAcademico::class, 'idNivelAcademico', 'idNivelAcademico');
    }
}
