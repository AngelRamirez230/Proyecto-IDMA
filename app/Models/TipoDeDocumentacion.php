<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDeDocumentacion extends Model
{
    protected $table = 'Tipo_de_documentacion';
    protected $primaryKey = 'idTipoDeDocumentacion';
    public $timestamps = false;

    protected $fillable = [
        'nombreDocumento',
        'idSeccionDocumento'
    ];

    public function seccion()
    {
        return $this->belongsTo(
            SeccionDeDocumento::class,
            'idSeccionDocumento',
            'idSeccionDeDocumento'
        );
    }

    public function usuarios()
    {
        return $this->belongsToMany(
            Usuario::class,
            'Documentacion_de_usuario',
            'idTipoDeDocumento',
            'idUsuario'
        )->withPivot('ruta');
    }
}
