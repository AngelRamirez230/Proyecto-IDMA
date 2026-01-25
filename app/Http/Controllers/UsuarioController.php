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
use App\Models\Empleado;
use App\Models\Departamento;
use App\Models\NivelAcademico;
use App\Models\Docente;
use App\Models\RangoDeHorario;
use App\Models\DiaSemana;
use App\Services\BitacoraService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
            'departamentos'  => Departamento::orderBy('idDepartamento')->get(),
            'nivelesAcademicos' => NivelAcademico::orderBy('idNivelAcademico')->get(),
            'diasSemana'     => DiaSemana::orderBy('idDiaSemana')->get(),
            'rangosHorarios' => RangoDeHorario::orderBy('horaInicio')->orderBy('horaFin')->get(),
        ]);
    }

    /**
     * Guarda un nuevo usuario en la base de datos.
     */
    public function store(UsuarioRequest $request)
    {
        $rol = (int) $request->input('rol', 1);

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
        $usuario = Usuario::create([
            'primerNombre'            => $request->primer_nombre,
            'segundoNombre'           => $request->segundo_nombre,
            'primerApellido'          => $request->primer_apellido,
            'segundoApellido'         => $request->segundo_apellido,

            'idSexo'                  => $request->sexo,
            'idEstadoCivil'           => $request->estadoCivil,

            'telefono'                => $request->telefono,
            'correoInstitucional'     => $request->emailInstitucional,

            'nombreUsuario'           => $request->nombreUsuario,
            'contraseña'              => Hash::make($request->password),

            'fechaDeNacimiento'       => $request->fechaNacimiento,
            'RFC'                     => $request->rfc,
            'CURP'                    => $request->curp,
            'correoElectronico'       => $request->email,

            'idLocalidadNacimiento'   => $idLocalidadNacimiento,
            'idDomicilio'             => $domicilioId,

            'idtipoDeUsuario'         => $rol,
            'idestatus'               => 1,
        ]);

        $request->attributes->set('bitacora_usuario_afectado', $usuario->idUsuario);

        if ($rol === 2) {
            Empleado::create([
                'idUsuario'        => $usuario->idUsuario,
                'idDepartamento'   => $request->idDepartamento,
                'idNivelAcademico' => $request->idNivelAcademico,
            ]);
        }

        if ($rol === 3) {
            $docente = Docente::create([
                'idUsuario'        => $usuario->idUsuario,
                'idNivelAcademico' => $request->idNivelAcademico,
            ]);

            $horarios = $request->input('horarios', []);

            foreach ($horarios as $horario) {
                $idDiaSemana = $horario['idDiaSemana'] ?? null;
                $idRango = $horario['idRangoDeHorario'] ?? null;

                if (!$idDiaSemana) {
                    continue;
                }

                if (!$idRango || $idRango === 'manual') {
                    $horaInicio = $horario['horaInicio'] ?? null;
                    $horaFin = $horario['horaFin'] ?? null;

                    if (!$horaInicio || !$horaFin) {
                        continue;
                    }

                    $rango = RangoDeHorario::create([
                        'horaInicio'     => $horaInicio,
                        'horaFin'        => $horaFin,
                        'idTipoDeEstatus' => 3,
                    ]);

                    $idRango = $rango->idRangoDeHorario;
                }

                if ($idRango) {
                    DB::table('Docente_dia_rango_de_horario')->insert([
                        'idDocente'       => $docente->idDocente,
                        'idRangoDeHorario'=> $idRango,
                        'idDiaSemana'     => $idDiaSemana,
                    ]);
                }
            }
        }

        $responsableId = Auth::user()->idUsuario ?? null;
        if ($responsableId) {
            app(BitacoraService::class)->registrar(
                BitacoraService::ACCION_CREAR,
                (int) $responsableId,
                (int) $usuario->idUsuario,
                'shared.moduloUsuarios.altaDeUsuario'
            );
            $request->attributes->set('bitacora_registrada', true);
        }

        return redirect()
            ->route('consultaUsuarios')
            ->with('success', 'Usuario creado correctamente');
    }

    public function consultaUsuarios(Request $request)
    {
        $request->attributes->set('bitacora_nombre_vista', 'shared.moduloUsuarios.consultaDeUsuarios');

        $buscar = $request->input('buscarUsuario');
        $filtro = $request->input('filtro');
        $orden  = $request->input('orden');

        /*
        |----------------------------------------------------------
        | QUERY BASE
        |----------------------------------------------------------
        */
        $usuariosQuery = Usuario::with(['tipoDeUsuario', 'estatus']);

        /*
        |----------------------------------------------------------
        | FILTRO PRINCIPAL POR ESTATUS
        |----------------------------------------------------------
        */
        if ($filtro === 'eliminados') {
            // SOLO eliminados
            $usuariosQuery->where('idestatus', 8);
        } else {
            // Cualquier otro caso → excluir eliminados
            $usuariosQuery->where('idestatus', '!=', 8);

            if ($filtro === 'activos') {
                $usuariosQuery->where('idestatus', 1);
            } elseif ($filtro === 'suspendidos') {
                $usuariosQuery->where('idestatus', 2);
            }
            // 'todos' o vacío → ya queda cubierto
        }

        /*
        |----------------------------------------------------------
        | BÚSQUEDA
        |----------------------------------------------------------
        */
        if (!empty($buscar)) {
            $usuariosQuery->where(function ($q) use ($buscar) {
                $q->whereRaw(
                    "CONCAT_WS(' ', primerNombre, segundoNombre, primerApellido, segundoApellido) LIKE ?",
                    ["%{$buscar}%"]
                )
                ->orWhere('correoInstitucional', 'LIKE', "%{$buscar}%")
                ->orWhere('correoElectronico', 'LIKE', "%{$buscar}%")
                ->orWhereHas('tipoDeUsuario', function ($q2) use ($buscar) {
                    $q2->where('nombreTipoDeUsuario', 'LIKE', "%{$buscar}%");
                });
            });
        }

        /*
        |----------------------------------------------------------
        | ORDEN
        |----------------------------------------------------------
        */
        if ($orden === 'alfabetico') {
            $usuariosQuery->orderBy('primerApellido')
                        ->orderBy('primerNombre');
        } elseif ($orden === 'recientes') {
            $usuariosQuery->orderByDesc('idUsuario');
        } else {
            $usuariosQuery->orderByDesc('idUsuario');
        }

        $usuarios = $usuariosQuery
            ->paginate(10)
            ->withQueryString();

        return view('shared.moduloUsuarios.consultaDeUsuarios', compact(
            'usuarios', 'buscar', 'filtro', 'orden'
        ));
    }

    public function show(Usuario $usuario)
    {
        request()->attributes->set('bitacora_usuario_afectado', $usuario->idUsuario);
        request()->attributes->set('bitacora_nombre_vista', 'shared.moduloUsuarios.detalleDeUsuario');

        $usuario->load([
            'tipoDeUsuario',
            'estatus',
            'sexo',
            'estadoCivil',
            'domicilio.localidad.municipio.entidad.pais',
            'localidadNacimiento.municipio.entidad.pais',
        ]);

        // ViewModel simple para no ensuciar Blade
        $vm = [
            'nombreCompleto' => trim(collect([
                $usuario->primerNombre,
                $usuario->segundoNombre,
                $usuario->primerApellido,
                $usuario->segundoApellido,
            ])->filter()->implode(' ')),
        ];

        return view('shared.moduloUsuarios.detalleDeUsuario', compact('usuario', 'vm'));
    }

    public function edit(Usuario $usuario)
    {
        request()->attributes->set('bitacora_usuario_afectado', $usuario->idUsuario);
        request()->attributes->set('bitacora_nombre_vista', 'shared.moduloUsuarios.editarDeUsuario');

        $usuario->load([
            'sexo',
            'estadoCivil',
            'tipoDeUsuario',
            'estatus',
            'domicilio.localidad.municipio.entidad.pais',
            'localidadNacimiento.municipio.entidad.pais',
        ]);

        // Domicilio: derivar entidad/municipio desde localidad
        $domEntidadId = optional(optional(optional($usuario->domicilio)->localidad)->municipio)->idEntidad;
        $domMunicipioId = optional(optional($usuario->domicilio)->localidad)->idMunicipio;
        $domLocalidadId = optional($usuario->domicilio)->idLocalidad;

        // Nacimiento: derivar entidad/municipio desde localidadNacimiento
        $nacEntidadId = optional(optional($usuario->localidadNacimiento)->municipio)->idEntidad;
        $nacMunicipioId = optional($usuario->localidadNacimiento)->idMunicipio;
        $nacLocalidadId = $usuario->idLocalidadNacimiento;

        // País nacimiento (si es por catálogo México, típicamente viene de la entidad->pais)
        $paisNacimientoId = optional(optional(optional($usuario->localidadNacimiento)->municipio)->entidad)->idPais;

        return view('shared.moduloUsuarios.editarDeUsuario', [
            'usuario'            => $usuario,
            'sexos'              => Sexo::orderBy('nombreSexo')->get(),
            'estadosCiviles'     => EstadoCivil::orderBy('nombreEstadoCivil')->get(),
            'entidades'          => Entidad::orderBy('nombreEntidad')->get(),
            'paises'             => Pais::orderBy('nombrePais')->get(),

            // precarga (para JS)
            'domEntidadId'       => $domEntidadId,
            'domMunicipioId'     => $domMunicipioId,
            'domLocalidadId'     => $domLocalidadId,

            'paisNacimientoId'   => $paisNacimientoId,
            'nacEntidadId'       => $nacEntidadId,
            'nacMunicipioId'     => $nacMunicipioId,
            'nacLocalidadId'     => $nacLocalidadId,
        ]);
    }

    public function update(UsuarioRequest $request, Usuario $usuario)
    {
        $request->attributes->set('bitacora_usuario_afectado', $usuario->idUsuario);
        $request->attributes->set('bitacora_nombre_vista', 'shared.moduloUsuarios.editarDeUsuario');

        /*
        |----------------------------------------------------------
        | 1) LOCALIDAD DE NACIMIENTO
        |----------------------------------------------------------
        */
        $idLocalidadNacimiento = $request->input('localidadNacimiento'); // puede ser null si extranjero

        /*
        |----------------------------------------------------------
        | 2) LOCALIDAD DE DOMICILIO (CATÁLOGO O MANUAL)
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
        | 3) UPSERT DOMICILIO (si hay datos)
        |----------------------------------------------------------
        */
        $tieneDatosDomicilio = $idLocalidadDomicilio
            || $request->filled('calle')
            || $request->filled('codigoPostal')
            || $request->filled('colonia')
            || $request->filled('numeroExterior')
            || $request->filled('numeroInterior');

        $domicilioId = $usuario->idDomicilio;

        if ($tieneDatosDomicilio) {

            if ($usuario->domicilio) {
                $usuario->domicilio->update([
                    'codigoPostal'   => $request->codigoPostal,
                    'calle'          => $request->calle,
                    'numeroExterior' => $request->numeroExterior,
                    'numeroInterior' => $request->numeroInterior,
                    'colonia'        => $request->colonia,
                    'idLocalidad'    => $idLocalidadDomicilio,
                ]);
            } else {
                $dom = Domicilio::create([
                    'codigoPostal'   => $request->codigoPostal,
                    'calle'          => $request->calle,
                    'numeroExterior' => $request->numeroExterior,
                    'numeroInterior' => $request->numeroInterior,
                    'colonia'        => $request->colonia,
                    'idLocalidad'    => $idLocalidadDomicilio,
                ]);

                $domicilioId = $dom->idDomicilio;
            }
        }

        /*
        |----------------------------------------------------------
        | 4) ACTUALIZAR USUARIO (password solo si viene)
        |----------------------------------------------------------
        */
        $data = [
            'primerNombre'          => $request->primer_nombre,
            'segundoNombre'         => $request->segundo_nombre,
            'primerApellido'        => $request->primer_apellido,
            'segundoApellido'       => $request->segundo_apellido,
            'idSexo'                => $request->sexo,
            'idEstadoCivil'         => $request->estadoCivil,
            'telefono'              => $request->telefono,
            'telefonoFijo'          => $request->telefonoFijo,
            'correoInstitucional'   => $request->emailInstitucional,
            'nombreUsuario'         => $request->nombreUsuario,
            'fechaDeNacimiento'     => $request->fechaNacimiento,
            'RFC'                   => $request->rfc,
            'CURP'                  => $request->curp,
            'correoElectronico'     => $request->email,
            'idLocalidadNacimiento' => $idLocalidadNacimiento,
            'idDomicilio'           => $domicilioId,
        ];

        // Solo si el usuario escribió nueva contraseña
        if ($request->filled('password')) {
            $data['contraseña'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()
            ->route('consultaUsuarios')
            ->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(Usuario $usuario)
    {
        request()->attributes->set('bitacora_usuario_afectado', $usuario->idUsuario);
        request()->attributes->set('bitacora_nombre_vista', 'shared.moduloUsuarios.consultaDeUsuarios');

        // Evitar eliminar usuarios ya inactivos (opcional)
        if ((int) $usuario->idestatus === 8) {
            return redirect()
                ->route('consultaUsuarios')
                ->with('warning', 'El usuario ya está eliminado.');
        }

        // Estatus 8 = Eliminado
        $usuario->update([
            'idestatus' => 8,
        ]);

        return redirect()
            ->route('consultaUsuarios')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    public function toggleEstatus(Usuario $usuario)
    {
        request()->attributes->set('bitacora_usuario_afectado', $usuario->idUsuario);
        request()->attributes->set('bitacora_nombre_vista', 'shared.moduloUsuarios.consultaDeUsuarios');

        $estatusActual = (int) $usuario->idestatus;

        // Reglas:
        // 1 <-> 2
        // 8 -> 1 (lo “reactiva” sin crear función de recuperación)
        if ($estatusActual === 2) {
            $nuevoEstatus = 1; // Habilitar
            $mensaje = 'Usuario habilitado correctamente.';
        } elseif ($estatusActual === 1) {
            $nuevoEstatus = 2; // Suspender
            $mensaje = 'Usuario suspendido correctamente.';
        } elseif ($estatusActual === 8) {
            $nuevoEstatus = 2; // Eliminado -> Activo
            $mensaje = 'Usuario reactivado y suspendido correctamente.';
        } else {
            // Cualquier otro estatus que exista en tu catálogo
            // lo mandamos a 1 por seguridad (o puedes bloquearlo)
            $nuevoEstatus = 1;
            $mensaje = 'Estatus actualizado correctamente.';
        }

        $usuario->update([
            'idestatus' => $nuevoEstatus,
        ]);

        return redirect()
            ->route('consultaUsuarios')
            ->with('success', $mensaje);
    }
}
