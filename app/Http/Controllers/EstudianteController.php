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

            /* ===== LOCALIDAD DOMICILIO ===== */
            $idLocalidadDomicilio = $request->localidad;

            if (!$idLocalidadDomicilio && $request->filled('localidadManual') && $request->filled('municipio')) {
                $localidad = Localidad::firstOrCreate(
                    [
                        'nombreLocalidad' => $request->localidadManual,
                        'idMunicipio'     => $request->municipio,
                    ],
                    ['idTipoDeEstatus' => 3]
                );

                $idLocalidadDomicilio = $localidad->idLocalidad;
            }

            /* ===== DOMICILIO ===== */
            $domicilioId = null;
            if ($idLocalidadDomicilio || $request->filled('calle')) {
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
                    $u->where('primerNombre', 'like', "%$buscar%")
                        ->orWhere('primerApellido', 'like', "%$buscar%");
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
            'usuario.domicilio',
            'usuario.localidadNacimiento'
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

        DB::transaction(function () use ($request, $usuario, $estudiante) {

            /* ===== USUARIO ===== */
            $usuario->update([
                'primerNombre'        => $request->primer_nombre,
                'segundoNombre'       => $request->segundo_nombre,
                'primerApellido'      => $request->primer_apellido,
                'segundoApellido'     => $request->segundo_apellido,
                'telefono'            => $request->telefono,
                'correoInstitucional' => $request->correoInstitucional,
                'idSexo'              => $request->idSexo,
                'idEstadoCivil'       => $request->idEstadoCivil,
                'fechaDeNacimiento'   => $request->fechaNacimiento,
                'RFC'                 => $request->RFC,
                'CURP'                => $request->CURP,
            ]);

            /* ===== ESTUDIANTE ===== */
            $estudiante->update([
                'grado'            => $request->grado,
                'idPlanDeEstudios' => $request->idPlanDeEstudios,
            ]);
        });

        return redirect()
            ->route('estudiantes.index')
            ->with('success', 'Estudiante actualizado correctamente');
    }



    public function destroy($id)
    {
    
    }

}
