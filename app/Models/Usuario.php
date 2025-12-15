<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    /**
     * Nombre real de la tabla
     */
    protected $table = 'Usuario';

    /**
     * Llave primaria
     */
    protected $primaryKey = 'idUsuario';

    /**
     * La tabla no usa timestamps
     */
    public $timestamps = false;

    /**
     * Campos asignables de forma masiva
     */
    protected $fillable = [
        // ======================
        // DATOS PERSONALES
        // ======================
        'primerNombre',
        'segundoNombre',
        'primerApellido',
        'segundoApellido',

        'idSexo',
        'idEstadoCivil',

        'fechaDeNacimiento',
        'RFC',
        'CURP',

        // ======================
        // CONTACTO
        // ======================
        'telefono',
        'telefonoFijo',
        'correoInstitucional',
        'correoElectronico',

        // ======================
        // ACCESO
        // ======================
        'nombreUsuario',
        'contraseña',

        // ======================
        // UBICACIÓN
        // ======================
        'idLocalidadNacimiento',
        'idDomicilio',

        // ======================
        // CONTROL
        // ======================
        'idtipoDeUsuario',
        'idestatus',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES ELOQUENT
    |--------------------------------------------------------------------------
    */

    /**
     * Usuario pertenece a un Sexo
     */
    public function sexo()
    {
        return $this->belongsTo(Sexo::class, 'idSexo', 'idSexo');
    }

    /**
     * Usuario pertenece a un Estado Civil
     */
    public function estadoCivil()
    {
        return $this->belongsTo(EstadoCivil::class, 'idEstadoCivil', 'idEstadoCivil');
    }

    /**
     * Usuario pertenece a un Tipo de Usuario
     */
    public function tipoDeUsuario()
    {
        return $this->belongsTo(
            TipoDeUsuario::class,
            'idtipoDeUsuario',
            'idTipoDeUsuario'
        );
    }

    /**
     * Usuario pertenece a un Estatus
     */
    public function estatus()
    {
        return $this->belongsTo(
            TipoDeEstatus::class,
            'idestatus',
            'idTipoDeEstatus'
        );
    }

    /**
     * Usuario pertenece a un Domicilio
     */
    public function domicilio()
    {
        return $this->belongsTo(
            Domicilio::class,
            'idDomicilio',
            'idDomicilio'
        );
    }

    /**
     * Lugar de nacimiento del usuario
     */
    public function localidadNacimiento()
    {
        return $this->belongsTo(
            Localidad::class,
            'idLocalidadNacimiento',
            'idLocalidad'
        );
    }
}
