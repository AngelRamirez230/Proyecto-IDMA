<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// MODELOS
use App\Models\Usuario;
use App\Models\Estudiante;
use App\Models\Sexo;
use App\Models\EstadoCivil;
use App\Models\Entidad;
use App\Models\Municipio;
use App\Models\Localidad;
use App\Models\Domicilio;
use App\Models\Pais;
use App\Models\PlanDeEstudios;
use App\Models\Generacion;
use App\Models\TipoDeInscripcion;

class EstudianteController extends Controller
{

    /**
     * Mostrar formulario de alta de estudiante
     */
    public function create()
    {
        $mesActual = date('n');
        $añoActual = date('Y');

        $generacionActual = Generacion::where('añoDeInicio', $añoActual)
            ->where('idMesInicio', $mesActual)
            ->first();

        return view('shared.moduloEstudiantes.altaEstudiante', [
            'sexos'              => Sexo::orderBy('nombreSexo')->get(),
            'estadosCiviles'     => EstadoCivil::orderBy('nombreEstadoCivil')->get(),
            'entidades'          => Entidad::orderBy('nombreEntidad')->get(),
            'municipios'         => collect(),
            'localidades'        => collect(),
            'paises'             => Pais::orderBy('nombrePais')->get(),
            'planes'             => PlanDeEstudios::orderBy('nombrePlanDeEstudios')->get(),
            'tipoInscripcion'   => TipoDeInscripcion::orderBy('nombreTipoDeInscripcion')->get(),
            'generacionActual'   => $generacionActual
        ]);
    }

    /**
     * Guardar estudiante
     */
    public function store(Request $request)
    {


        $validator = Validator::make(
            $request->all(),
            [
                // Datos personales
                'primer_nombre'     => 'required|string|max:50',
                'primer_apellido'   => 'required|string|max:50',
                'sexo'              => 'required|exists:sexo,idSexo',
                'estadoCivil'       => 'required|exists:estado_civil,idEstadoCivil',
                'fechaNacimiento'   => 'required|date',

                // Contacto
                'telefono'          => 'required|string|max:15|unique:usuario,telefono',
                'email'             => 'nullable|email|unique:usuario,correoElectronico',
                'emailInstitucional'=> 'nullable|email|unique:usuario,correoInstitucional',

                // Usuario
                'nombreUsuario'     => 'required|string|unique:usuario,nombreUsuario',
                'password'          => 'required|string|min:8',

                // Estudiante
                'matriculaNumerica'     => 'required|numeric|unique:estudiante,matriculaNumerica',
                'matriculaAlfanumerica' => 'required|string|unique:estudiante,matriculaAlfanumerica',
                'grado'                 => 'required|integer|min:1',

                // Relaciones
                'generacion'        => 'required|exists:generacion,idGeneracion',
                'planEstudios'      => 'required|exists:plan_de_estudios,idPlanDeEstudios',
                'tipoInscripcion'   => 'required|exists:tipo_de_inscripcion,idTipoDeInscripcion',

                // Nacimiento
                'localidadNacimiento' => 'required|exists:localidad,idLocalidad',
            ],
            [
                // 🔹 MENSAJES PERSONALIZADOS
                'required' => 'El campo :attribute es obligatorio.',
                'string'   => 'El campo :attribute debe ser texto.',
                'max'      => 'El campo :attribute no debe exceder :max caracteres.',
                'min'      => 'El campo :attribute debe tener al menos :min caracteres.',
                'email'    => 'El campo :attribute debe ser un correo válido.',
                'numeric'  => 'El campo :attribute debe ser numérico.',
                'integer'  => 'El campo :attribute debe ser un número entero.',
                'unique'   => 'El valor ingresado en :attribute ya está registrado.',
                'exists'   => 'La opción seleccionada en :attribute no es válida.',
                'date'     => 'El campo :attribute debe ser una fecha válida.',
            ],
            [
                'primer_nombre'           => 'primer nombre',
                'primer_apellido'         => 'primer apellido',
                'sexo'                    => 'sexo',
                'estadoCivil'             => 'estado civil',
                'fechaNacimiento'         => 'fecha de nacimiento',
                'telefono'                => 'teléfono',
                'email'                   => 'correo electrónico',
                'emailInstitucional'      => 'correo institucional',
                'nombreUsuario'           => 'nombre de usuario',
                'password'                => 'contraseña',
                'matriculaNumerica'       => 'matrícula numérica',
                'matriculaAlfanumerica'   => 'matrícula alfanumérica',
                'grado'                   => 'grado',
                'generacion'              => 'generación',
                'planEstudios'            => 'plan de estudios',
                'tipoInscripcion'         => 'tipo de inscripción',
                'localidadNacimiento'     => 'localidad de nacimiento',
            ]
        );

        // 🔴 SI FALLA → POPUP + DATOS CONSERVADOS
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)   // mantiene x-error-field
                ->with('popupError', 'No se pudo crear el estudiante, verifique bien los datos')
                ->withInput();              // conserva los valores
        }
        DB::transaction(function () use ($request) {

            // Localidad de nacimiento
            $idLocalidadNacimiento = $request->localidadNacimiento;

            // Localidad domicilio
            $idLocalidadDomicilio = null;
            if ($request->filled('localidad')) {
                $idLocalidadDomicilio = $request->localidad;
            } elseif ($request->filled('localidadManual') && $request->filled('municipio')) {
                $localidadExistente = Localidad::where('nombreLocalidad', $request->localidadManual)
                    ->where('idMunicipio', $request->municipio)
                    ->first();

                if ($localidadExistente) {
                    $idLocalidadDomicilio = $localidadExistente->idLocalidad;
                } else {
                    $localidad = Localidad::create([
                        'nombreLocalidad' => $request->localidadManual,
                        'idMunicipio'     => $request->municipio,
                        'idTipoDeEstatus' => 3, // Pendiente
                    ]);
                    $idLocalidadDomicilio = $localidad->idLocalidad;
                }
            }

            // Crear domicilio
            $domicilioId = null;
            if ($idLocalidadDomicilio || $request->filled('calle') || $request->filled('codigoPostal')) {
                $domicilio = Domicilio::create([
                    'codigoPostal'   => $request->codigoPostal,
                    'calle'          => $request->calle,
                    'numeroExterior' => $request->numeroExterior,
                    'numeroInterior' => $request->numeroInterior,
                    'colonia'        => $request->colonia,
                    'idLocalidad'    => $idLocalidadDomicilio,
                ]);
                $domicilioId = $domicilio->idDomicilio;
            }

            // Validar teléfono único
            if (Usuario::where('telefono', $request->telefono)->exists()) {
                return back()->with('popupError', 'El teléfono ya está registrado')->withInput();
            }

            // Validar generación
            $idGeneracion = $request->generacion;
            if (!$idGeneracion || !Generacion::find($idGeneracion)) {
                return back()->with('popupError', 'Generación inválida')->withInput();
            }

            // Crear usuario
            $usuario = Usuario::create([
                'primerNombre'          => $request->primer_nombre,
                'segundoNombre'         => $request->segundo_nombre,
                'primerApellido'        => $request->primer_apellido,
                'segundoApellido'       => $request->segundo_apellido,
                'idSexo'                => $request->sexo,
                'idEstadoCivil'         => $request->estadoCivil,
                'fechaDeNacimiento'     => $request->fechaNacimiento,
                'RFC'                   => $request->rfc,
                'CURP'                  => $request->curp,
                'telefono'              => $request->telefono,
                'correoInstitucional'   => $request->emailInstitucional,
                'correoElectronico'     => $request->email,
                'nombreUsuario'         => $request->nombreUsuario,
                'contraseña'            => Hash::make($request->password),
                'idLocalidadNacimiento' => $idLocalidadNacimiento,
                'idDomicilio'           => $domicilioId,
                'idtipoDeUsuario'       => 4,
                'idestatus'             => 1,
            ]);

            // Crear estudiante
            Estudiante::create([
                'idUsuario'             => $usuario->idUsuario,
                'matriculaNumerica'     => $request->matriculaNumerica,
                'matriculaAlfanumerica' => $request->matriculaAlfanumerica,
                'grado'                 => $request->grado,
                'creditosAcumulados'    => 0,
                'promedioGeneral'       => 0,
                'fechaDeIngreso'        => now(),
                'idGeneracion'          => $idGeneracion,
                'idTipoDeInscripcion'   => $request->tipoInscripcion,
                'idPlanDeEstudios'      => $request->planEstudios,
                'idEstatus'             => 1,
            ]);
        });

        return redirect()
            ->route('apartadoEstudiantes')
            ->with('success', 'Estudiante registrado correctamente');
    }
}
