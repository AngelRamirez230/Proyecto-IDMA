<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentacionDeUsuario extends Model
{
    protected $table = 'Documentacion_de_usuario';
    public $timestamps = false;

    protected $fillable = [
        'idUsuario',
        'idTipoDeDocumento',
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

    public function tipoDeDocumento()
    {
        return $this->belongsTo(
            TipoDeDocumentacion::class,
            'idTipoDeDocumento',
            'idTipoDeDocumentacion'
        );
    }
}
