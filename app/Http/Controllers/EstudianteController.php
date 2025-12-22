<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        // Mes y año actuales
        $mesActual = date('n'); // 1-12
        $añoActual = date('Y');

        // Buscar si ya existe la generación actual
        $generacionActual = Generacion::where('añoDeInicio', $añoActual)
            ->where('idMes', $mesActual)
            ->first();

        // Si no existe, crear automáticamente
        if (!$generacionActual) {
            $generacionActual = Generacion::create([
                'añoDeInicio' => $añoActual,
                'idMes' => $mesActual,
                'idEstatus' => 1, // Activa
            ]);
        }

        // Construir clave visual 22A/22B
        $claveGeneracion = substr($añoActual, -2) . (($mesActual <= 6) ? 'A' : 'B');
        return view('shared.moduloEstudiantes.altaEstudiante', [
            'sexos'            => Sexo::orderBy('nombreSexo')->get(),
            'estadosCiviles'   => EstadoCivil::orderBy('nombreEstadoCivil')->get(),
            'entidades'        => Entidad::orderBy('nombreEntidad')->get(),
            'municipios'       => collect(),
            'localidades'      => collect(),
            'paises'           => Pais::orderBy('nombrePais')->get(),

            // Catálogos académicos
            'planes' => PlanDeEstudios::orderBy('nombrePlanDeEstudios')->get(),
            'generaciones'     => Generacion::orderBy('añoDeInicio')->get(),
            'tiposInscripcion' => TipoDeInscripcion::orderBy('nombreTipoDeInscripcion')->get(),
            'generacionActualId' => $generacionActual->idGeneracion,
            'claveGeneracion'    => $claveGeneracion
        ]);
    }

    /**
     * Guardar estudiante
     */
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            /*
            |----------------------------------------------------------
            | 1. LOCALIDAD DE NACIMIENTO (OBLIGATORIA)
            |----------------------------------------------------------
            */
            $idLocalidadNacimiento = $request->localidadNacimiento;

            /*
            |----------------------------------------------------------
            | 2. LOCALIDAD DE DOMICILIO (CATÁLOGO O MANUAL)
            |----------------------------------------------------------
            */
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

            /*
            |----------------------------------------------------------
            | 3. CREAR DOMICILIO (SI APLICA)
            |----------------------------------------------------------
            */
            $domicilioId = null;

            if (
                $idLocalidadDomicilio ||
                $request->filled('calle') ||
                $request->filled('codigoPostal')
            ) {
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

            /*
            |----------------------------------------------------------
            | 4. CREAR USUARIO (ESTUDIANTE)
            |----------------------------------------------------------
            */
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
                'correoInstitucional'   => $request->correoInstitucional,
                'correoElectronico'     => $request->correo,

                'nombreUsuario'         => $request->nombreUsuario,
                'contraseña'            => Hash::make($request->password),

                'idLocalidadNacimiento' => $idLocalidadNacimiento,
                'idDomicilio'           => $domicilioId,

                'idtipoDeUsuario'       => 4, // ESTUDIANTE
                'idestatus'             => 1,
            ]);

            /*
            |----------------------------------------------------------
            | 5. CREAR ESTUDIANTE
            |----------------------------------------------------------
            */
            Estudiante::create([
                'idUsuario'              => $usuario->idUsuario,
                'matriculaNumerica'      => $request->matriculaNumerica,
                'matriculaAlfanumerica'  => $request->matriculaAlfanumerica,
                'grado'                  => $request->grado,
                'creditosAcumulados'     => 0,
                'promedioGeneral'        => 0,
                'fechaDeIngreso'         => now(),
                'idGeneracion'           => $request->generacion,
                'idTipoDeInscripcion'    => $request->tipoInscripcion,
                'idPlanDeEstudios'       => $request->planEstudios,
                'idEstatus'              => 1,
            ]);
        });

        return redirect()
            ->route('consultaEstudiantes')
            ->with('success', 'Estudiante registrado correctamente');
    }
}
