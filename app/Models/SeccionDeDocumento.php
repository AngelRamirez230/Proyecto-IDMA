<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeccionDeDocumento extends Model
{
    protected $table = 'Seccion_de_documento';
    protected $primaryKey = 'idSeccionDeDocumento';
    public $timestamps = false;

    protected $fillable = [
        'nombreSeccion'
    ];

    public function tiposDeDocumentacion()
    {
        return $this->hasMany(
            TipoDeDocumentacion::class,'idSeccionDocumento','idSeccionDeDocumento'
        );
    }
}
