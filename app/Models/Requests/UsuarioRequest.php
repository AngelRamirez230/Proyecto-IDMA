<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            // =======================
            // DATOS PERSONALES
            // =======================
            'primer_nombre'      => ['required', 'string', 'max:45'],
            'segundo_nombre'     => ['nullable', 'string', 'max:45'],

            'primer_apellido'    => ['required', 'string', 'max:45'],
            'segundo_apellido'   => ['nullable', 'string', 'max:45'],

            'sexo'               => ['required', 'integer', 'exists:Sexo,idSexo'],

            'telefono'           => ['nullable', 'string', 'digits:10'],

            'emailInstitucional' => ['nullable', 'email', 'max:100',
                                     'unique:Usuario,correoInstitucional'],

            'password'           => ['required', 'string', 'min:8', 'max:255'],

            'nombreUsuario'      => ['required', 'string', 'max:100'],

            'fechaNacimiento'    => ['nullable', 'date', 'before_or_equal:today'],

            'curp'               => ['nullable', 'string', 'max:18'],
            'rfc'                => ['nullable', 'string', 'max:13'],

            'email'              => ['nullable', 'email', 'max:100',
                                     'unique:Usuario,correoElectronico'],


            // =======================
            // SELECCIÓN DE DOMICILIO
            // =======================
            'entidad_id'         => ['nullable', 'integer', 'exists:Entidad,idEntidad'],
            'municipio_id'       => ['nullable', 'integer', 'exists:Municipio,idMunicipio'],
            'localidad_id'       => ['nullable', 'integer', 'exists:Localidad,idLocalidad'],

            // Datos textuales opcionales
            'codigoPostal'       => ['nullable', 'string', 'max:10'],
            'calle'              => ['nullable', 'string', 'max:150'],
            'numeroExterior'     => ['nullable', 'string', 'max:20'],
            'numeroInterior'     => ['nullable', 'string', 'max:20'],
            'colonia'            => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'primer_nombre'      => 'primer nombre',
            'segundo_nombre'     => 'segundo nombre',
            'primer_apellido'    => 'primer apellido',
            'segundo_apellido'   => 'segundo apellido',
            'sexo'               => 'sexo',
            'emailInstitucional' => 'correo institucional',
            'nombreUsuario'      => 'nombre de usuario',
            'fechaNacimiento'    => 'fecha de nacimiento',
            'curp'               => 'CURP',
            'rfc'                => 'RFC',
            'email'              => 'correo electrónico',
            'entidad_id'         => 'entidad',
            'municipio_id'       => 'municipio',
            'localidad_id'       => 'localidad',
        ];
    }
}
