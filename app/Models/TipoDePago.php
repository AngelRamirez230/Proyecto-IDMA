<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDePago extends Model
{
    protected $table = 'Tipo_de_pago';

    protected $primaryKey = 'idTipoDePago';

    public $timestamps = false;

    protected $fillable = [
        'nombreTipoDePago'
    ];

    // =============================
    // RELACIONES
    // =============================

    public function pagos()
    {
        return $this->hasMany(
            Pago::class,
            'idTipoDePago',
            'idTipoDePago'
        );
    }
}
