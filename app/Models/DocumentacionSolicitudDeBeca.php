<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentacionSolicitudDeBeca extends Model
{
    protected $table = 'Documentacion_solicitud_de_beca';

    protected $primaryKey = 'idDocumentacionSolicitudDeBeca';

    public $timestamps = false;

    protected $fillable = [
        'idEstudiante',
        'idTipoDeDocumentacion',
        'idSolicitudDeBeca',
        'ruta',
    ];

    /* =========================
       RELACIONES
    ========================= */

    
    public function estudiante()
    {
        return $this->belongsTo(
            Estudiante::class,
            'idEstudiante',
            'idEstudiante'
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

    
    public function solicitudDeBeca()
    {
        return $this->belongsTo(
            SolicitudDeBeca::class,
            'idSolicitudDeBeca',
            'idSolicitudDeBeca'
        );
    }
}
