<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    // Nombre real de la tabla
    protected $table = 'Usuario';

    // Primary key
    protected $primaryKey = 'idUsuario';

    // La tabla NO usa created_at ni updated_at
    public $timestamps = false;

    // Campos asignables de manera masiva
    protected $fillable = [
        'primerNombre',
        'segundoNombre',
        'primerApellido',
        'segundoApellido',
        'idSexo',
        'telefono',
        'correoInstitucional',
        'nombreUsuario',
        'contraseña',
        'fechaDeNacimiento',
        'RFC',
        'CURP',
        'correoElectronico',
        'domicilio',
        'idtipoDeUsuario',
        'idestatus',
        'idDomicilio',
    ];

    /*
     * ===========================
     *     RELACIONES ELOQUENT
     * ===========================
     */

    /**
     * Relación con Sexo (antes Genero)
     * Usuario pertenece a un Sexo
     */
    public function sexo()
    {
        return $this->belongsTo(Sexo::class, 'idSexo', 'idSexo');
    }

    /**
     * Relación con Tipo de Usuario
     */
    public function tipoDeUsuario()
    {
        return $this->belongsTo(TipoDeUsuario::class, 'idtipoDeUsuario', 'idTipoDeUsuario');
    }

    /**
     * Relación con Estatus
     */
    public function estatus()
    {
        return $this->belongsTo(TipoDeEstatus::class, 'idestatus', 'idTipoDeEstatus');
    }

    /**
     * Relación con Domicilio
     */
    public function domicilio()
    {
        return $this->belongsTo(Domicilio::class, 'idDomicilio', 'idDomicilio');
    }


    public function estudiante()
    {
        return $this->hasOne(Estudiante::class, 'idUsuario', 'idUsuario');
    }
}
