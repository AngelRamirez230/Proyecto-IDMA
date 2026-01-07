<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentacionDeUsuario extends Model
{
    protected $table = 'documentacion_de_usuario';
    public $timestamps = false;

    protected $fillable = [
        'idUsuario',
        'idTipoDeDocumentacion',
        'ruta'
    ];

    public function usuario()
    {
        return $this->belongsTo(
            Usuario::class,
            'idUsuario',
            'idUsuario'
        );
    }

    public function tipoDeDocumentacion()
    {
        return $this->belongsTo(
            TipoDeDocumentacion::class,
            'idTipoDeDocumentacion',
            'idTipoDeDocumentacion'
        );
    }
}
