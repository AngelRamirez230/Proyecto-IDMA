<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\RangoDeHorario;

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
            'rol' => $isUpdate
                ? ['nullable', 'integer', Rule::in([1, 2, 3, 4])]
                : ['required', 'integer', Rule::in([1, 2, 3, 4])],

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
               EMPLEADO
            ======================= */
            'idDepartamento' => [
                'nullable',
                'integer',
                'required_if:rol,2',
                'exists:Departamento,idDepartamento',
            ],

            'idNivelAcademico' => [
                'nullable',
                'integer',
                'required_if:rol,2,3',
                'exists:Nivel_academico,idNivelAcademico',
            ],

            /* =======================
               DOCENTE
            ======================= */
            'horarios' => [
                'nullable',
                'array',
                'required_if:rol,3',
                'min:1',
            ],

            'horarios.*.idDiaSemana' => [
                'nullable',
                'integer',
                'required_if:rol,3',
                'exists:Dia_semana,idDiaSemana',
            ],

            'horarios.*.idRangoDeHorario' => [
                'nullable',
            ],

            'horarios.*.horaInicio' => [
                'nullable',
                'date_format:H:i',
            ],

            'horarios.*.horaFin' => [
                'nullable',
                'date_format:H:i',
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ((int) $this->input('rol') !== 3) {
                return;
            }

            $horarios = $this->input('horarios');

            if (!is_array($horarios) || count($horarios) === 0) {
                $validator->errors()->add('horarios', 'Debes registrar al menos un horario.');
                return;
            }

            foreach ($horarios as $index => $horario) {
                $idRango = $horario['idRangoDeHorario'] ?? null;
                $horaInicio = $horario['horaInicio'] ?? null;
                $horaFin = $horario['horaFin'] ?? null;

                $usaManual = ($idRango === 'manual') || (!$idRango && ($horaInicio || $horaFin));

                if ($idRango && $idRango !== 'manual') {
                    if (!is_numeric($idRango) || !RangoDeHorario::where('idRangoDeHorario', $idRango)->exists()) {
                        $validator->errors()->add("horarios.$index.idRangoDeHorario", 'El rango seleccionado no es valido.');
                    }
                }

                if ($usaManual) {
                    if (!$horaInicio || !$horaFin) {
                        $validator->errors()->add("horarios.$index.horaInicio", 'Debes indicar hora inicio y fin.');
                    }
                }

                if (!$idRango && !$horaInicio && !$horaFin) {
                    $validator->errors()->add("horarios.$index.idRangoDeHorario", 'Selecciona un rango o captura horas.');
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'rol'                 => 'rol',
            'idDepartamento'       => 'departamento',
            'idNivelAcademico'     => 'nivel academico',
            'horarios'            => 'horarios',
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
            'curp.max'          => 'El CURP no debe tener mas de 18 caracteres.',
            'rfc.max'           => 'El RFC no debe tener más de 13 caracteres.',
            'email.email'       => 'El correo electrónico no tiene un formato válido.',
            'email.unique'      => 'El correo electrónico ya está registrado.',
            'emailInstitucional.email'  => 'El correo institucional no tiene un formato válido.',
            'emailInstitucional.unique' => 'El correo institucional ya está registrado.',
            'nombreUsuario.unique' => 'El nombre de usuario ya esta registrado.',
            'localidadNacimiento.required_if' =>
                'La localidad de nacimiento es obligatoria cuando el pais de nacimiento es Mexico.',
            'localidad.required_without' =>
                'Debes seleccionar una localidad o escribir una manualmente.',
            'localidadManual.required_without' =>
                'Debes escribir la localidad si no seleccionas una del catálogo.',
        ];
    }
}


