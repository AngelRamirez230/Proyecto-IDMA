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

            /* =======================
               DATOS PERSONALES
            ======================= */
            'primer_nombre'    => ['required', 'string', 'max:45'],
            'segundo_nombre'   => ['nullable', 'string', 'max:45'],

            'primer_apellido'  => ['required', 'string', 'max:45'],
            'segundo_apellido' => ['nullable', 'string', 'max:45'],

            'sexo' => [
                'required',
                'integer',
                'exists:Sexo,idSexo'
            ],

            // NUEVO: ESTADO CIVIL (CATÁLOGO)
            'estadoCivil' => [
                'required',
                'integer',
                'exists:Estado_civil,idEstadoCivil'
            ],

            'telefono' => ['nullable', 'digits:10'],

            'emailInstitucional' => [
                'nullable',
                'email',
                'max:100',
                'unique:Usuario,correoInstitucional'
            ],

            'password' => ['required', 'string', 'min:8'],

            'nombreUsuario' => ['required', 'string', 'max:100'],

            'fechaNacimiento' => [
                'nullable',
                'date',
                'before_or_equal:today'
            ],

            'curp' => ['nullable', 'string', 'max:18'],
            'rfc'  => ['nullable', 'string', 'max:13'],

            'email' => [
                'nullable',
                'email',
                'max:100',
                'unique:Usuario,correoElectronico'
            ],

            /* =======================
                LUGAR DE NACIMIENTO
            ======================= */
            'paisNacimiento' => ['required', 'integer', 'exists:Pais,idPais'],

            // México → selects
            'localidadNacimiento' => [
                'nullable',
                'integer',
                'exists:Localidad,idLocalidad',
                'required_if:paisNacimiento,1', // ASUME que 1 = México
            ],

            // Extranjero → inputs manuales
            'localidadNacimientoManual' => [
                'nullable',
                'string',
                'max:150',
                'required_unless:paisNacimiento,1',
            ],

            /* =======================
               DOMICILIO
            ======================= */
            'entidad' => [
                'nullable',
                'integer',
                'exists:Entidad,idEntidad'
            ],

            'municipio' => [
                'nullable',
                'integer',
                'exists:Municipio,idMunicipio'
            ],

            // Localidad seleccionada (catálogo)
            'localidad' => [
                'nullable',
                'integer',
                'exists:Localidad,idLocalidad',
                'required_without:localidadManual'
            ],

            // Localidad escrita manualmente
            'localidadManual' => [
                'nullable',
                'string',
                'max:150',
                'required_without:localidad'
            ],

            'codigoPostal'   => ['nullable', 'string', 'max:10'],
            'calle'          => ['nullable', 'string', 'max:150'],
            'numeroExterior' => ['nullable', 'string', 'max:20'],
            'numeroInterior' => ['nullable', 'string', 'max:20'],
            'colonia'        => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'primer_nombre'        => 'primer nombre',
            'primer_apellido'      => 'primer apellido',
            'sexo'                 => 'sexo',
            'estadoCivil'          => 'estado civil',
            'emailInstitucional'   => 'correo institucional',
            'nombreUsuario'        => 'nombre de usuario',
            'fechaNacimiento'      => 'fecha de nacimiento',

            // Nacimiento
            'localidadNacimiento'  => 'localidad de nacimiento',

            // Domicilio
            'entidad'              => 'entidad',
            'municipio'            => 'municipio',
            'localidad'            => 'localidad',
            'localidadManual'      => 'localidad (manual)',
        ];
    }

    public function messages(): array
    {
        return [

            /* =======================
            PASSWORD
            ======================= */
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',

            /* =======================
            RFC
            ======================= */
            'rfc.max' => 'El RFC no debe tener más de 13 caracteres.',

            /* =======================
            EMAIL PERSONAL
            ======================= */
            'email.email'  => 'El correo electrónico no tiene un formato válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',

            /* =======================
            EMAIL INSTITUCIONAL
            ======================= */
            'emailInstitucional.email'  => 'El correo institucional no tiene un formato válido.',
            'emailInstitucional.unique' => 'El correo institucional ya está registrado.',

            /* =======================
            LOCALIDAD
            ======================= */
            'localidad.required_without' =>
                'Debes seleccionar una localidad o escribir una manualmente.',

            'localidadManual.required_without' =>
                'Debes escribir la localidad si no seleccionas una del catálogo.',
        ];
    }
}
