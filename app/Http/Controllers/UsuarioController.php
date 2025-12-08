<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Muestra el formulario de alta de usuario.
     */
    public function create()
    {
        // Catálogos para selects
        // $sexos = Sexo::all();
        // $entidades = Entidad::all();
        // $municipios = Municipio::all();
        // $localidades = Localidad::all();

        return view('shared.moduloUsuarios.altaDeUsuario');
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     */
    public function store(UsuarioRequest $request)
    {
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

            // Este campo se usará cuando crees la tabla de Domicilio real
            'idDomicilio'        => null,

            // Valores por defecto
            'idtipoDeUsuario'    => 1,
            'idestatus'          => 1,
        ]);

        return redirect()
            ->route('consultaUsuarios')
            ->with('success', 'Usuario creado correctamente');
    }
}
