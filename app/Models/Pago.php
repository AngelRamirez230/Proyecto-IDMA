<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Estudiante;
use App\Models\ConceptoDePago;
use App\Models\TipoDeEstatus;
use App\Models\TipoDePago;

class Pago extends Model
{
    protected $table = 'Pago';

    protected $primaryKey = 'Referencia';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    // =============================
    // CAMPOS ASIGNABLES
    // =============================
    protected $fillable = [
        'Referencia',
        'fechaDePago',
        'idConceptoDePago',
        'costoConceptoOriginal',
        'nombreBeca',
        'porcentajeDeDescuento',
        'descuentoDeBeca',
        'descuentoDePago',
        'referenciaOriginal',
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
        'idEstudiante',
    ];

    // =============================
    // CASTS
    // =============================
    protected $casts = [
        'fechaDePago'           => 'datetime',
        'fechaGeneracionDePago' => 'datetime',
        'fechaLimiteDePago'     => 'datetime',

        'costoConceptoOriginal' => 'decimal:2',
        'porcentajeDeDescuento' => 'decimal:2',
        'descuentoDeBeca'       => 'decimal:2',
        'descuentoDePago'       => 'decimal:2',
        'montoAPagar'           => 'decimal:2',
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

    // =============================
    // RELACIÓN RECURSIVA (RECARGOS)
    // =============================

    /**
     * Pago original (sin recargo)
     */
    public function pagoOriginal()
    {
        return $this->belongsTo(
            self::class,
            'referenciaOriginal',
            'Referencia'
        );
    }

    /**
     * Pagos derivados (con recargo)
     */
    public function recargos()
    {
        return $this->hasMany(
            self::class,
            'referenciaOriginal',
            'Referencia'
        );
    }
}
