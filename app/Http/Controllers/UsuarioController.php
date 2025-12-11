<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\Usuario;
use App\Models\Sexo;
use App\Models\Entidad;
use App\Models\Municipio;
use App\Models\Localidad;
use App\Models\Domicilio;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Muestra el formulario de alta de usuario.
     */
    public function create()
    {
        // 1. Recuperar el rol desde el query string
        $rol = request()->query('rol');

        // Validar que el rol sea un número entre 1 y 4
        if (!in_array($rol, [1, 2, 3, 4])) {
            abort(404, "Rol no válido.");
        }

        // 2. Nombres de roles
        $roles = [
            1 => 'Administrador',
            2 => 'Empleado',
            3 => 'Docente',
            4 => 'Estudiante'
        ];

        $nombreRol = $roles[$rol];

        // 3. Catálogos reales desde BD
        $sexos = Sexo::all();
        $entidades = Entidad::all();

        // Para municipios y localidades, normalmente se cargarán vía AJAX.
        $municipios = Municipio::where('idEntidad', $entidades->first()->idEntidad ?? null)->get();
        $localidades = collect(); // Inicialmente vacío

        return view('shared.moduloUsuarios.altaDeUsuario', [
            'rol'        => $rol,
            'nombreRol'  => $nombreRol,
            'sexos'      => $sexos,
            'entidades'  => $entidades,
            'municipios' => $municipios,
            'localidades'=> $localidades,
        ]);
    }

    /**
     * Guarda un nuevo usuario en la base de datos.
     */
    public function store(UsuarioRequest $request)
{
    // 1. Crear domicilio si se proporcionaron datos
    $domicilioId = null;

    if ($request->filled('localidad') || 
        $request->filled('calle') || 
        $request->filled('codigoPostal')) 
    {
        $domicilio = Domicilio::create([
            'codigoPostal'   => $request->codigoPostal,
            'calle'          => $request->calle,
            'numeroExterior' => $request->numeroExterior,
            'numeroInterior' => $request->numeroInterior,
            'colonia'        => $request->colonia,
            'idLocalidad'    => $request->localidad,
        ]);

        $domicilioId = $domicilio->idDomicilio;
    }
        
    // 2. Crear usuario asociado al domicilio
    Usuario::create([
        'primerNombre'       => $request->primer_nombre,
        'segundoNombre'      => $request->segundo_nombre,
        'primerApellido'     => $request->primer_apellido,
        'segundoApellido'    => $request->segundo_apellido,

        'idSexo'             => $request->sexo,
        'telefono'           => $request->telefono,
        'correoInstitucional'=> $request->emailInstitucional,

        'nombreUsuario'      => $request->nombreUsuario,
        'contraseña'         => Hash::make($request->password),

        'fechaDeNacimiento'  => $request->fechaNacimiento,
        'RFC'                => $request->rfc,
        'CURP'               => $request->curp,
        'correoElectronico'  => $request->email,

        'idDomicilio'        => $domicilioId,

        'idtipoDeUsuario'    => session('rol_seleccionado', 1),
        'idestatus'          => 1,
    ]);

    return redirect()
        ->route('consultaUsuarios')
        ->with('success', 'Usuario creado correctamente');
    }
}
