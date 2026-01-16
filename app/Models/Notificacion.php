<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificacion';
    protected $primaryKey = 'idNotificacion';
    public $timestamps = false;

    protected $fillable = [
        'idUsuario',
        'titulo',
        'mensaje',
        'tipoDeNotificacion',
        'fechaDeInicio',
        'fechaFin',
        'leida',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoDeNotificacion::class, 'tipoDeNotificacion', 'idTipoDeNotificacion');
    }
}
