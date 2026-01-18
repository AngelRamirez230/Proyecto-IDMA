<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoDeNotificacion extends Model
{
    use HasFactory;

    protected $table = 'TipoDeNotificacion';
    protected $primaryKey = 'idTipoDeNotificacion';
    public $timestamps = false;

    protected $fillable = ['nombreTipoDeNotificacion'];

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'tipoDeNotificacion', 'idTipoDeNotificacion');
    }
}
