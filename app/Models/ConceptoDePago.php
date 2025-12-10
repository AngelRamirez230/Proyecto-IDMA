<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptoDePago extends Model
{
    protected $table = 'concepto_de_pago'; // Nombre de la tabla
    protected $primaryKey = 'idConceptoDePago'; // Llave primaria

    public $timestamps = false; // Si tu tabla NO tiene created_at y updated_at

    protected $fillable = [
        'nombreConceptoDePago',
        'costo',
        'idUnidad',
        'idEstatus'
    ];

    // Si quieres relación con tipo_de_unidad:
    public function unidad()
    {
        return $this->belongsTo(TipoDeUnidad::class, 'idUnidad', 'idTipoDeUnidad');
    }

    // Relación con tipo_de_estatus
    public function estatus()
    {
        return $this->belongsTo(TipoDeEstatus::class, 'idEstatus', 'idTipoDeEstatus');
    }



}
