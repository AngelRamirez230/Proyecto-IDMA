<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'numeroDeOperaciónBAZ',
        'numeroDeSucursal',
        'comisión',
        'ImporteNeto',
        'IVA',
        'ImporteDePago',
        'idTipoDePago',
        'tipoDeRegistro',
        'fechaGeneracionDePago',
        'idEstatus',
        'idEstudiante'
    ];

    // =============================
    // RELACIONES (opcional)
    // =============================

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'idEstudiante');
    }
}
