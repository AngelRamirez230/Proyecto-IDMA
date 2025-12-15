<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCivil extends Model
{
    protected $table = 'Estado_civil';
    protected $primaryKey = 'idEstadoCivil';
    public $timestamps = false;

    protected $fillable = [
        'nombreEstadoCivil',
        'idTipoDeEstatus',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Un estado civil puede estar asociado a muchos usuarios
     */
    public function usuarios()
    {
        return $this->hasMany(
            Usuario::class,
            'idEstadoCivil',
            'idEstadoCivil'
        );
    }

    /**
     * Relación con estatus (activo / pendiente / inactivo)
     */
    public function estatus()
    {
        return $this->belongsTo(
            TipoDeEstatus::class,
            'idTipoDeEstatus',
            'idTipoDeEstatus'
        );
    }
}
