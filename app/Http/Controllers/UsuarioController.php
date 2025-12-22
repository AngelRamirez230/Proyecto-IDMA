<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\Usuario;
use App\Models\Sexo;
use App\Models\EstadoCivil;
use App\Models\Entidad;
use App\Models\Municipio;
use App\Models\Localidad;
use App\Models\Domicilio; 
use App\Models\Pais;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Muestra el formulario de alta de usuario.
     */
    public function create()
    {
        $rol = request()->query('rol');

        if (!in_array($rol, [1, 2, 3, 4])) {
            abort(404, 'Rol no válido.');
        }

        $roles = [
            1 => 'Administrador',
            2 => 'Empleado',
            3 => 'Docente',
            4 => 'Estudiante',
        ];

        return view('shared.moduloUsuarios.altaDeUsuario', [
            'rol'            => $rol,
            'nombreRol'      => $roles[$rol],
            'sexos'          => Sexo::orderBy('nombreSexo')->get(),
            'estadosCiviles' => EstadoCivil::orderBy('nombreEstadoCivil')->get(),
            'entidades'      => Entidad::orderBy('nombreEntidad')->get(),
            'municipios'     => collect(),
            'localidades'    => collect(),
            'paises'         => Pais::orderBy('nombrePais')->get(),
        ]);
    }

    /**
     * Guarda un nuevo usuario en la base de datos.
     */
    public function store(UsuarioRequest $request)
    {
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
        | 4. CREAR USUARIO
        |----------------------------------------------------------
        */
        Usuario::create([
            'primerNombre'            => $request->primer_nombre,
            'segundoNombre'           => $request->segundo_nombre,
            'primerApellido'          => $request->primer_apellido,
            'segundoApellido'         => $request->segundo_apellido,

            'idSexo'                  => $request->sexo,
            'idEstadoCivil'           => $request->estadoCivil,

            'telefono'                => $request->telefono,
            'correoInstitucional'     => $request->emailInstitucional,

            'nombreUsuario'           => $request->nombreUsuario,
            'contrasena'              => Hash::make($request->password),

            'fechaDeNacimiento'       => $request->fechaNacimiento,
            'RFC'                     => $request->rfc,
            'CURP'                    => $request->curp,
            'correoElectronico'       => $request->email,

            'idLocalidadNacimiento'   => $idLocalidadNacimiento,
            'idDomicilio'             => $domicilioId,

            'idtipoDeUsuario'         => session('rol_seleccionado', 1),
            'idestatus'               => 1,
        ]);

        return redirect()
            ->route('consultaUsuarios')
            ->with('success', 'Usuario creado correctamente');
    }
}
