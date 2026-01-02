<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    private function isUpdate(): bool
    {
        return $this->isMethod('PUT') || $this->isMethod('PATCH');
    }

    /**
     * Obtiene el idUsuario del modelo enlazado a la ruta o del parámetro directo.
     */
    private function routeUserId(): ?int
    {
        $routeParam = $this->route('usuario'); // puede ser Modelo Usuario o un id

        if (is_object($routeParam) && isset($routeParam->idUsuario)) {
            return (int) $routeParam->idUsuario;
        }

        if (is_numeric($routeParam)) {
            return (int) $routeParam;
        }

        return null;
    }

    public function rules(): array
    {
        $isUpdate  = $this->isUpdate();
        $idUsuario = $this->routeUserId(); // clave para ignore()

        return [

            /* =======================
               DATOS PERSONALES
            ======================= */
            'primer_nombre'    => ['required', 'string', 'max:45'],
            'segundo_nombre'   => ['nullable', 'string', 'max:45'],

            'primer_apellido'  => ['required', 'string', 'max:45'],
            'segundo_apellido' => ['nullable', 'string', 'max:45'],

            'sexo' => ['required', 'integer', 'exists:Sexo,idSexo'],
            'estadoCivil' => ['required', 'integer', 'exists:Estado_civil,idEstadoCivil'],

            'telefono'     => ['nullable', 'digits:10'],
            'telefonoFijo' => ['nullable', 'digits:10'],

            'emailInstitucional' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('Usuario', 'correoInstitucional')
                    ->ignore($isUpdate ? $idUsuario : null, 'idUsuario'),
            ],

            // En update NO es obligatoria; si viene, debe cumplir min:8
            'password' => $isUpdate
                ? ['nullable', 'string', 'min:8']
                : ['required', 'string', 'min:8'],

            'nombreUsuario' => [
                'required',
                'string',
                'max:100',
                Rule::unique('Usuario', 'nombreUsuario')
                    ->ignore($isUpdate ? $idUsuario : null, 'idUsuario'),
            ],

            'fechaNacimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'curp' => ['nullable', 'string', 'max:18'],
            'rfc'  => ['nullable', 'string', 'max:13'],

            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('Usuario', 'correoElectronico')
                    ->ignore($isUpdate ? $idUsuario : null, 'idUsuario'),
            ],

            /* =======================
                LUGAR DE NACIMIENTO
            ======================= */
            'paisNacimiento' => ['required', 'integer', 'exists:Pais,idPais'],

            // México → catálogo
            'localidadNacimiento' => [
                'nullable',
                'integer',
                'exists:Localidad,idLocalidad',
                'required_if:paisNacimiento,1', // 1 = México
            ],

            // Extranjero → manual
            'localidadNacimientoManual' => [
                'nullable',
                'string',
                'max:150',
                'required_unless:paisNacimiento,1',
            ],

            /* =======================
               DOMICILIO
            ======================= */
            'entidad'   => ['nullable', 'integer', 'exists:Entidad,idEntidad'],
            'municipio' => ['nullable', 'integer', 'exists:Municipio,idMunicipio'],

            'localidad' => [
                'nullable',
                'integer',
                'exists:Localidad,idLocalidad',
                'required_without:localidadManual'
            ],

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
            'telefonoFijo'         => 'teléfono fijo',
            'emailInstitucional'   => 'correo institucional',
            'nombreUsuario'        => 'nombre de usuario',
            'fechaNacimiento'      => 'fecha de nacimiento',
            'localidadNacimiento'  => 'localidad de nacimiento',
            'entidad'              => 'entidad',
            'municipio'            => 'municipio',
            'localidad'            => 'localidad',
            'localidadManual'      => 'localidad (manual)',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'rfc.max'           => 'El RFC no debe tener más de 13 caracteres.',
            'email.email'       => 'El correo electrónico no tiene un formato válido.',
            'email.unique'      => 'El correo electrónico ya está registrado.',
            'emailInstitucional.email'  => 'El correo institucional no tiene un formato válido.',
            'emailInstitucional.unique' => 'El correo institucional ya está registrado.',
            'localidad.required_without' =>
                'Debes seleccionar una localidad o escribir una manualmente.',
            'localidadManual.required_without' =>
                'Debes escribir la localidad si no seleccionas una del catálogo.',
        ];
    }
}
