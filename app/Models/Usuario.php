<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class Usuario extends Authenticatable
{
    use Notifiable;

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

    public function sexo()
    {
        return $this->belongsTo(Sexo::class, 'idSexo', 'idSexo');
    }

    public function estadoCivil()
    {
        return $this->belongsTo(EstadoCivil::class, 'idEstadoCivil', 'idEstadoCivil');
    }

    public function tipoDeUsuario()
    {
        return $this->belongsTo(
            TipoDeUsuario::class,
            'idtipoDeUsuario',
            'idTipoDeUsuario'
        );
    }

    public function estatus()
    {
        return $this->belongsTo(
            TipoDeEstatus::class,
            'idestatus',
            'idTipoDeEstatus'
        );
    }

    public function domicilio()
    {
        return $this->belongsTo(
            Domicilio::class,
            'idDomicilio',
            'idDomicilio'
        );
    }

    public function localidadNacimiento()
    {
        return $this->belongsTo(
            Localidad::class,
            'idLocalidadNacimiento',
            'idLocalidad'
        );
    }

    public function estudiante()
    {
        return $this->hasOne(Estudiante::class, 'idUsuario', 'idUsuario');
    }

    public function documentaciones()
    {
        return $this->hasMany(
            DocumentacionDeUsuario::class,
            'idUsuario',
            'idUsuario'
        );
    }

    public function empleado()
    {
        return $this->hasOne(
            Empleado::class,
            'idUsuario',   // FK en Empleado
            'idUsuario'    // PK en Usuario
        );
    }

    /*
    |----------------------------------------------------------------------
    | HELPERS DE ROLES Y DEPARTAMENTOS
    |----------------------------------------------------------------------
    */

    // ¿Es administrador?
    public function esAdmin()
    {
        return $this->idtipoDeUsuario == 1;
    }

    // ¿Es empleado?
    public function esEmpleado()
    {
        return $this->empleado !== null;
    }

    /**
     * Verifica si el usuario es empleado de uno o varios departamentos
     *
     * @param int|array $departamentos
     * @return bool
     */
    public function esEmpleadoDe($departamentos)
    {
        if (!$this->esEmpleado()) {
            return false;
        }

        return in_array(
            $this->empleado->idDepartamento,
            (array) $departamentos
        );
    }
}
