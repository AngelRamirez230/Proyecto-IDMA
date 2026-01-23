<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Estudiante;
use App\Models\ConceptoDePago;
use App\Models\Estatus;
use App\Models\TipoDePago;

class Pago extends Model
{
    protected $table = 'Pago';

    protected $primaryKey = 'Referencia';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'Referencia',
        'fechaDePago',
        'idConceptoDePago',
        'montoAPagar',
        'numeroDeOperaciónBAZ',
        'numeroDeSucursal',
        'comisión',
        'ImporteNeto',
        'IVA',
        'ImporteDePago',
        'idTipoDePago',
        'tipoDeRegistro',
        'fechaGeneracionDePago',
        'fechaLimiteDePago',
        'aportacion',
        'idEstatus',
        'idEstudiante'
    ];


    protected $casts = [
        'fechaDePago' => 'datetime',
        'fechaGeneracionDePago' => 'datetime',
        'fechaLimiteDePago'     => 'datetime',
    ];


    // =============================
    // RELACIONES
    // =============================

    public function estudiante()
    {
        return $this->belongsTo(
            Estudiante::class,
            'idEstudiante',
            'idEstudiante'
        );
    }

    public function concepto()
    {
        return $this->belongsTo(
            ConceptoDePago::class,
            'idConceptoDePago',
            'idConceptoDePago'
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


    public function tipoDePago()
    {
        return $this->belongsTo(
            TipoDePago::class,
            'idTipoDePago',
            'idTipoDePago'
        );
    }

}
