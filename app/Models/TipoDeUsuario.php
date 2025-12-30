<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDeUsuario extends Model
{
    protected $table = 'Tipo_de_usuario';
    protected $primaryKey = 'idTipoDeUsuario';
    public $timestamps = false;

    protected $fillable = [
        'nombreTipoDeUsuario',
    ];
}
