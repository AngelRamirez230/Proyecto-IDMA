<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudDeBeca extends Model
{
    protected $table = 'solicitudDeBeca';
    protected $primaryKey = 'idSolicitudDeBeca';
    public $timestamps = false;

    protected $fillable = [
        'idEstudiante',
        'idBeca',
        'promedioAnterior',
        'examenExtraordinario',
        'observacion',
        'fechaDeSolicitud',
        'fechaDeConclusion',
        'idEstatus'
    ];


    protected $casts = [
        'fechaDeSolicitud'  => 'date',
        'fechaDeConclusion' => 'date',
    ];

    // =====================
    // RELACIONES
    // =====================

    public function estudiante()
    {
        return $this->belongsTo(
            Estudiante::class,
            'idEstudiante',
            'idEstudiante'
        );
    }

    public function beca()
    {
        return $this->belongsTo(
            Beca::class,
            'idBeca',
            'idBeca'
        );
    }

    public function estatus()
    {
        return $this->belongsTo(
            TipoDeEstatus::class,
            'idEstatus',
            'idTipoDeEstatus'
        );
    }

    public function documentaciones()
    {
        return $this->hasMany(
            DocumentacionSolicitudDeBeca::class,
            'idSolicitudDeBeca',
            'idSolicitudDeBeca'
        );
    }

    public function scopeDelEstudiante($query, $idEstudiante)
    {
        return $query->where('idEstudiante', $idEstudiante);
    }
}
