<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDeInscripcion extends Model
{
    protected $table = 'Tipo_de_inscripcion';
    protected $primaryKey = 'idTipoDeInscripcion';

    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'nombreTipoDeInscripcion'
    ];

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'idTipoDeInscripcion', 'idTipoDeInscripcion');
    }
}
