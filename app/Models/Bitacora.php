<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'Bitacora';

    protected $primaryKey = 'idBitacora';

    public $timestamps = false;

    protected $fillable = [
        'tipoDeAccion',
        'idUsuarioResponsable',
        'idUsuarioAfectado',
        'fecha',
        'nombreVista',
    ];

    public function usuarioResponsable()
    {
        return $this->belongsTo(Usuario::class, 'idUsuarioResponsable', 'idUsuario');
    }

    public function usuarioAfectado()
    {
        return $this->belongsTo(Usuario::class, 'idUsuarioAfectado', 'idUsuario');
    }

    public function getAccionNombreAttribute()
    {
        return match ((int) $this->tipoDeAccion) {
            1 => 'Crear',
            2 => 'Leer',
            3 => 'Actualizar',
            4 => 'Eliminar',
            default => 'Desconocido',
        };
    }
}
