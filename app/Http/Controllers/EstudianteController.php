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
     * Mostrar formulario
     */
    public function create()
    {
        $mesActual = now()->month;
        $añoActual = now()->year;

        $generacionActual = Generacion::where('añoDeInicio', $añoActual)
            ->where('idMesInicio', $mesActual)
            ->first();

        return view('shared.moduloEstudiantes.altaEstudiante', [
            'sexos'            => Sexo::orderBy('nombreSexo')->get(),
            'estadosCiviles'   => EstadoCivil::orderBy('nombreEstadoCivil')->get(),
            'entidades'        => Entidad::orderBy('nombreEntidad')->get(),
            'municipios'       => collect(),
            'localidades'      => collect(),
            'paises'           => Pais::orderBy('nombrePais')->get(),
            'planes'           => PlanDeEstudios::orderBy('nombrePlanDeEstudios')->get(),
            'tipoInscripcion'  => TipoDeInscripcion::orderBy('nombreTipoDeInscripcion')->get(),
            'generacionActual' => $generacionActual
        ]);
    }

    /**
     * Guardar estudiante
     */
    public function store(Request $request)
    {
        /* ================= VALIDACIONES ================= */
        $validator = Validator::make(
            $request->all(),
            [
                // USUARIO
                'primer_nombre'       => 'required|string|max:45',
                'segundo_nombre'      => 'nullable|string|max:45',
                'primer_apellido'     => 'required|string|max:45',
                'segundo_apellido'    => 'nullable|string|max:45',
                'telefono'            => 'nullable|string|max:10|unique:usuario,telefono',
                'telefonoFijo'        => 'nullable|string|max:10|unique:usuario,telefonoFijo',
                'correoInstitucional' => 'required|email|max:100|unique:usuario,correoInstitucional',
                'correoElectronico'   => 'nullable|email|max:100|unique:usuario,correoElectronico',
                'nombreUsuario'       => 'required|string|max:100|unique:usuario,nombreUsuario',
                'contraseña'          => 'required|string|min:8',
                'fechaNacimiento'     => 'required|date',
                'CURP'                => 'nullable|string|max:18|unique:usuario,CURP',
                'RFC'                 => 'nullable|string|max:13|unique:usuario,RFC',
                'idSexo'              => 'required|exists:sexo,idSexo',
                'idEstadoCivil'       => 'required|exists:estado_civil,idEstadoCivil',
                
                // NACIMIENTO
                'paisNacimiento'      => 'required',
                'entidadNacimiento'   => 'required|exists:entidad,idEntidad',
                'municipioNacimiento' => 'required|exists:municipio,idMunicipio',
                'localidadNacimiento' => 'required|exists:localidad,idLocalidad',

                // DOMICILIO
                'entidad'   => 'required|exists:entidad,idEntidad',
                'municipio' => 'required|exists:municipio,idMunicipio',
                'localidad' => 'required|exists:localidad,idLocalidad',

                

                // ESTUDIANTE
                'grado'                 => 'required|integer|min:1|max:9',
                'idGeneracion'          => 'required|exists:generacion,idGeneracion',
                'idPlanDeEstudios'      => 'required|exists:plan_de_estudios,idPlanDeEstudios',
                'idTipoDeInscripcion'   => 'required|exists:tipo_de_inscripcion,idTipoDeInscripcion',

            ],
            [
                'required' => 'El campo :attribute es obligatorio.',
                'string'   => 'El campo :attribute debe ser texto.',
                'max'      => 'El campo :attribute no debe exceder :max caracteres.',
                'min'      => 'El campo :attribute debe tener al menos :min caracteres.',
                'email'    => 'El campo :attribute debe ser un correo válido.',
                'integer'  => 'El campo :attribute debe ser un número entero.',
                'unique'   => 'El valor ingresado en :attribute ya está registrado.',
                'exists'   => 'La opción seleccionada en :attribute no es válida.',
                'date'     => 'El campo :attribute debe ser una fecha válida.',
            ],
            [
                // ======================
                // NOMBRES AMIGABLES
                // ======================
                'primer_nombre'       => 'primer nombre',
                'segundo_nombre'      => 'segundo nombre',
                'primer_apellido'     => 'primer apellido',
                'segundo_apellido'    => 'segundo apellido',
                'telefono'            => 'teléfono',
                'telefonoFijo'        => 'teléfono fijo',
                'correoInstitucional' => 'correo institucional',
                'correoElectronico'   => 'correo electrónico',
                'nombreUsuario'       => 'nombre de usuario',
                'contraseña'          => 'contraseña',
                'fechaNacimiento'     => 'fecha de nacimiento',
                'CURP'                => 'CURP',
                'RFC'                 => 'RFC',
                'idSexo'              => 'sexo',
                'idEstadoCivil'       => 'estado civil',
                'grado'               => 'grado',
                'idGeneracion'        => 'generación',
                'idPlanDeEstudios'    => 'plan de estudios',
                'idTipoDeInscripcion' => 'tipo de inscripción',
                'paisNacimiento'      => 'país de nacimiento',
                'entidadNacimiento'   => 'entidad de nacimiento',
                'municipioNacimiento' => 'municipio de nacimiento',
                'localidadNacimiento' => 'localidad de nacimiento',
                'entidad'   => 'entidad del domicilio',
                'municipio' => 'municipio del domicilio',
                'localidad' => 'localidad del domicilio',

            
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with('popupError', 'No se pudo crear el estudiante, verifique los datos ingresados')
                ->withInput();
        }

        /* ================= TRANSACCIÓN ================= */
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

            /* ===== USUARIO ===== */
            $usuario = Usuario::create([
                'primerNombre'          => $request->primer_nombre,
                'segundoNombre'         => $request->segundo_nombre,
                'primerApellido'        => $request->primer_apellido,
                'segundoApellido'       => $request->segundo_apellido,
                'idSexo'                => $request->idSexo,
                'idEstadoCivil'         => $request->idEstadoCivil,
                'fechaDeNacimiento'     => $request->fechaNacimiento,
                'RFC'                   => $request->RFC,
                'CURP'                  => $request->CURP,
                'telefono'              => $request->telefono,
                'telefonoFijo'          => $request->telefonoFijo,
                'correoInstitucional'   => $request->correoInstitucional,
                'correoElectronico'     => $request->correoElectronico,
                'nombreUsuario'         => $request->nombreUsuario,
                'contraseña'            => Hash::make($request->contraseña),
                'idLocalidadNacimiento' => $idLocalidadNacimiento,
                'idDomicilio'           => $domicilioId,
                'idtipoDeUsuario'       => 4,
                'idestatus'             => 1,
            ]);


            $matriculaAlfanumerica = $this->generarMatriculaAlfanumerica(
                $request->idGeneracion,
                $request->idPlanDeEstudios
            );

            $plan = PlanDeEstudios::with('licenciatura')
                ->findOrFail($request->idPlanDeEstudios);

            $matriculaNumerica = $this->generarMatriculaNumerica(
                $request->idGeneracion,
                $plan->licenciatura->idLicenciatura
            );

            /* ===== ESTUDIANTE ===== */
            Estudiante::create([
                'idUsuario'             => $usuario->idUsuario,
                'matriculaNumerica'     => $matriculaNumerica,
                'matriculaAlfanumerica' => $matriculaAlfanumerica,
                'grado'                 => $request->grado,
                'creditosAcumulados'    => 0,
                'promedioGeneral'       => 0.00,
                'fechaDeIngreso'        => now(),
                'idGeneracion'          => $request->idGeneracion,
                'idTipoDeInscripcion'   => $request->idTipoDeInscripcion,
                'idPlanDeEstudios'      => $request->idPlanDeEstudios,
                'idEstatus'             => 1,
            ]);
        });

        return redirect()
            ->route('apartadoEstudiantes')
            ->with('success', 'Estudiante registrado correctamente');
    }

    private function generarMatriculaAlfanumerica(int $idGeneracion,int $idPlanDeEstudios): string
    {
        $prefijo = 'IDMA';

        
        $generacion = Generacion::findOrFail($idGeneracion);
        $año = substr($generacion->añoDeInicio, -2);

        
        $periodo = ($generacion->idMesInicio == 3) ? 'A' : 'B';

       
        $plan = PlanDeEstudios::with('licenciatura')
            ->findOrFail($idPlanDeEstudios);

        $abreviacion = $plan->licenciatura->abreviacionLicenciatura;

        
        $ultimo = Estudiante::where('idGeneracion', $idGeneracion)
            ->where('idPlanDeEstudios', $idPlanDeEstudios)
            ->orderBy('matriculaAlfanumerica', 'desc')
            ->first();

        if ($ultimo) {
            $consecutivo = intval(substr($ultimo->matriculaAlfanumerica, -4)) + 1;
        } else {
            $consecutivo = 1;
        }

        $consecutivoFormateado = str_pad($consecutivo, 4, '0', STR_PAD_LEFT);

        return $prefijo. $año. $periodo. $abreviacion. $consecutivoFormateado;
    }

    private function generarMatriculaNumerica(int $idGeneracion, int $idLicenciatura): string
    {
        
        $siglaEscolar = '01';

        
        $ofertaAcademica = str_pad($idLicenciatura, 2, '0', STR_PAD_LEFT);

       
        $generacion = Generacion::findOrFail($idGeneracion);

        
        $año = $generacion->añoDeInicio;

        
        $periodo = ($generacion->idMesInicio == 3) ? '01' : '02';

        
        $ultimo = Estudiante::where('idGeneracion', $idGeneracion)
            ->orderBy('matriculaNumerica', 'desc')
            ->first();

        $consecutivo = $ultimo
            ? intval(substr($ultimo->matriculaNumerica, -4)) + 1
            : 1;

       
        $consecutivoFormateado = str_pad($consecutivo, 4, '0', STR_PAD_LEFT);

        
        return $siglaEscolar. $año. $periodo. $ofertaAcademica. $consecutivoFormateado;
    }



    public function index(Request $request)
    {
        $query = Estudiante::with([
            'usuario',
            'generacion',
            'planDeEstudios.licenciatura'
        ]);

        /* 🔍 BÚSQUEDA */
        if ($request->filled('buscarEstudiante')) {
            $buscar = $request->buscarEstudiante;

            $query->where(function ($q) use ($buscar) {
                $q->where('matriculaAlfanumerica', 'like', "%$buscar%")
                ->orWhere('matriculaNumerica', 'like', "%$buscar%")
                ->orWhereHas('usuario', function ($u) use ($buscar) {
                    $u->where('primerNombre', 'LIKE', "%{$buscar}%")
                        ->orWhere('segundoNombre', 'LIKE', "%{$buscar}%")
                        ->orWhere('primerApellido', 'LIKE', "%{$buscar}%")
                        ->orWhere('segundoApellido', 'LIKE', "%{$buscar}%");
                });
            });
        }

        /* FILTRO */
        if ($request->filtro === 'activos') {
            $query->whereHas('usuario', function ($q) {
                $q->where('idestatus', 1);
            });
        } elseif ($request->filtro === 'suspendidos') {
            $query->whereHas('usuario', function ($q) {
                $q->where('idestatus', 2);
            });
        }

        /* 🔠 ORDEN */
        if ($request->orden === 'alfabetico') {
            $query->join('usuario', 'estudiante.idUsuario', '=', 'usuario.idUsuario')
                ->orderBy('usuario.primerApellido')
                ->select('estudiante.*');
        }

        $estudiantes = $query->paginate(10)->withQueryString();

        return view('shared.moduloEstudiantes.consultaEstudiantes', [
            'estudiantes' => $estudiantes,
            'buscar'      => $request->buscarEstudiante,
            'filtro'      => $request->filtro,
            'orden'       => $request->orden,
        ]);
    }


    public function edit($id)
    {
        $estudiante = Estudiante::with([
            'usuario',
            'generacion',
            'planDeEstudios.licenciatura',
            'usuario.localidadNacimiento.municipio.entidad.pais',
            'usuario.domicilio.localidad.municipio.entidad.pais',
        ])->findOrFail($id);

        return view('shared.moduloEstudiantes.modificacionEstudiante', [
            'estudiante'        => $estudiante,
            'usuario'           => $estudiante->usuario,
            'sexos'             => Sexo::orderBy('nombreSexo')->get(),
            'estadosCiviles'    => EstadoCivil::orderBy('nombreEstadoCivil')->get(),
            'planes'            => PlanDeEstudios::orderBy('nombrePlanDeEstudios')->get(),
            'tipoInscripcion'   => TipoDeInscripcion::orderBy('nombreTipoDeInscripcion')->get(),
            'entidades'         => Entidad::orderBy('nombreEntidad')->get(),
            'municipios'        => Municipio::orderBy('nombreMunicipio')->get(),
            'localidades'       => Localidad::orderBy('nombreLocalidad')->get(),
            'paises'            => Pais::orderBy('nombrePais')->get(),
        ]);
    }


   public function update(Request $request, $id)
    {
        $estudiante = Estudiante::findOrFail($id);
        $usuario    = Usuario::findOrFail($estudiante->idUsuario);


        if ($request->accion === 'guardar') {

            /* ================= VALIDACIONES ================= */
            $validator = Validator::make(
                $request->all(),
                [
                    'primer_nombre'       => 'required|string|max:45',
                    'segundo_nombre'      => 'nullable|string|max:45',
                    'primer_apellido'     => 'required|string|max:45',
                    'segundo_apellido'    => 'nullable|string|max:45',

                    'telefono'            => 'required|string|max:10|unique:usuario,telefono,' . $usuario->idUsuario . ',idUsuario',
                    'telefonoFijo'        => 'nullable|string|max:10|unique:usuario,telefonoFijo,' . $usuario->idUsuario . ',idUsuario',

                    'correoInstitucional' => 'required|email|max:100|unique:usuario,correoInstitucional,' . $usuario->idUsuario . ',idUsuario',
                    'correoElectronico'   => 'nullable|email|max:100|unique:usuario,correoElectronico,' . $usuario->idUsuario . ',idUsuario',

                    'nombreUsuario'       => 'required|string|max:100|unique:usuario,nombreUsuario,' . $usuario->idUsuario . ',idUsuario',
                    'contraseña'          => 'nullable|string|min:8',

                    'fechaNacimiento'     => 'required|date',
                    'CURP'                => 'nullable|string|max:18|unique:usuario,CURP,' . $usuario->idUsuario . ',idUsuario',
                    'RFC'                 => 'nullable|string|max:13|unique:usuario,RFC,' . $usuario->idUsuario . ',idUsuario',

                    'idSexo'              => 'required|exists:sexo,idSexo',
                    'idEstadoCivil'       => 'required|exists:estado_civil,idEstadoCivil',

                    'grado'               => 'required|integer|min:1|max:9',
                    'idTipoDeInscripcion' => 'required|exists:tipo_de_inscripcion,idTipoDeInscripcion',
                ]
            );

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            /* ================= TRANSACCIÓN ================= */
            DB::transaction(function () use ($request, $usuario, $estudiante) {

                /* ===== DOMICILIO ===== */
                $domicilioId = $usuario->idDomicilio;

                if ($request->filled('localidad') || $request->filled('calle')) {

                    if ($domicilioId) {
                        $domicilio = Domicilio::find($domicilioId);
                        $domicilio->update([
                            'codigoPostal'   => $request->codigoPostal,
                            'calle'          => $request->calle,
                            'numeroExterior' => $request->numeroExterior,
                            'numeroInterior' => $request->numeroInterior,
                            'colonia'        => $request->colonia,
                            'idLocalidad'    => $request->localidad,
                        ]);
                    } else {
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
                }

                /* ===== LOCALIDAD NACIMIENTO ===== */
                $idLocalidadNacimiento = $request->localidadNacimiento;

                if (!$idLocalidadNacimiento && $request->filled('localidadNacimientoManual')) {
                    $localidadNacimiento = Localidad::firstOrCreate(
                        ['nombreLocalidad' => $request->localidadNacimientoManual],
                        ['idTipoDeEstatus' => 3]
                    );

                    $idLocalidadNacimiento = $localidadNacimiento->idLocalidad;
                }

                /* ===== USUARIO ===== */
                $dataUsuario = [
                    'primerNombre'          => $request->primer_nombre,
                    'segundoNombre'         => $request->segundo_nombre,
                    'primerApellido'        => $request->primer_apellido,
                    'segundoApellido'       => $request->segundo_apellido,
                    'telefono'              => $request->telefono,
                    'telefonoFijo'          => $request->telefonoFijo,
                    'correoInstitucional'   => $request->correoInstitucional,
                    'correoElectronico'     => $request->correoElectronico,
                    'nombreUsuario'         => $request->nombreUsuario,
                    'fechaDeNacimiento'     => $request->fechaNacimiento,
                    'RFC'                   => $request->RFC,
                    'CURP'                  => $request->CURP,
                    'idSexo'                => $request->idSexo,
                    'idEstadoCivil'         => $request->idEstadoCivil,
                    'idLocalidadNacimiento' => $idLocalidadNacimiento,
                    'idDomicilio'           => $domicilioId,
                ];

                if ($request->filled('contraseña')) {
                    $dataUsuario['contraseña'] = Hash::make($request->contraseña);
                }

                $usuario->update($dataUsuario);

                /* ===== ESTUDIANTE ===== */
                $estudiante->update([
                    'grado'               => $request->grado,
                    'idTipoDeInscripcion' => $request->idTipoDeInscripcion,
                ]);
            });

            return redirect()
                ->route('consultaEstudiantes')
                ->with('success', 'Estudiante actualizado correctamente.');
        }

        elseif ($request->accion === 'Suspender/Habilitar') {

            $estatusAnterior = $usuario->idestatus;

            $usuario->update([
                'idestatus' => ($usuario->idestatus == 1) ? 2 : 1
            ]);

            $nombreCompleto = trim(
                $usuario->primerNombre . ' ' .
                $usuario->segundoNombre . ' ' .
                $usuario->primerApellido . ' ' .
                $usuario->segundoApellido
            );

            $mensaje = ($estatusAnterior == 1)
                ? "El estudiante {$nombreCompleto} ha sido suspendido correctamente."
                : "El estudiante {$nombreCompleto} ha sido habilitado correctamente.";

            return redirect()
                ->route('consultaEstudiantes')
                ->with('success', $mensaje);
        }
    }





    public function destroy($id)
    {
    
    }

}
