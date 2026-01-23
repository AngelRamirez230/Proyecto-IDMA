<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

// MODELOS
use App\Models\Pago;
use App\Models\Estudiante;
use App\Models\ConceptoDePago;
use App\Models\Notificacion;

class PagoEstudianteController extends Controller
{
    // =============================
    // FORMULARIO
    // =============================
    public function create(Request $request)
    {
        $buscar = $request->buscar;
        $filtro = $request->filtro;
        $orden  = $request->orden;

        // =============================
        // QUERY BASE
        // =============================
        $query = Estudiante::with('usuario');

        // =============================
        // BUSCADOR
        // =============================
        if ($request->filled('buscar')) {

            $buscar = trim($buscar);

            $query->whereHas('usuario', function ($u) use ($buscar) {
                $u->where('primerNombre', 'LIKE', "%{$buscar}%")
                ->orWhere('segundoNombre', 'LIKE', "%{$buscar}%")
                ->orWhere('primerApellido', 'LIKE', "%{$buscar}%")
                ->orWhere('segundoApellido', 'LIKE', "%{$buscar}%")
                ->orWhereRaw(
                    "REPLACE(
                        TRIM(
                            CONCAT(
                                primerNombre, ' ',
                                IFNULL(segundoNombre, ''), ' ',
                                primerApellido, ' ',
                                IFNULL(segundoApellido, '')
                            )
                        ),
                        '  ', ' '
                    ) LIKE ?",
                    ["%{$buscar}%"]
                );
            });
        }

        // =============================
        // FILTRO POR ESTATUS
        // =============================
        if ($filtro === 'nuevoIngreso') {
            $query->where('grado', 1);
        }

        if ($filtro === 'inscritos') {
            $query->where('grado', '>', 1);
        }

        // =============================
        // ORDENAMIENTO
        // =============================
        if ($orden === 'alfabetico') {
            $query->join('usuario', 'usuario.idUsuario', '=', 'estudiante.idUsuario')
                ->orderBy('usuario.primerNombre')
                ->orderBy('usuario.primerApellido')
                ->orderBy('usuario.segundoApellido')
                ->select('estudiante.*');
        }

        // =============================
        // PAGINACIÓN
        // =============================
        $estudiantes = $query
            ->paginate(10)
            ->withQueryString();

        return view('SGFIDMA.moduloPagos.asignarPagoEstudiante', [
            'estudiantes' => $estudiantes,
            'conceptos'   => ConceptoDePago::where('idEstatus', 1)->get(),
            'buscar'      => $buscar,
        ]);
    }





    // =============================
    // GUARDAR PAGOS
    // =============================
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'idConceptoDePago'   => 'required|exists:concepto_de_pago,idConceptoDePago',
                'fechaEmisionDePago' => 'required|date|after_or_equal:today',
                'fechaLimiteDePago'  => 'required|date|after_or_equal:fechaEmisionDePago',
                'estudiantes'        => 'required|array|min:1',
                'estudiantes.*'      => 'exists:estudiante,idEstudiante',
                'aportacion'         => 'required|string|max:100',
            ],
            [
                'required' => 'El campo :attribute es obligatorio.',
                'string'   => 'El campo :attribute debe ser texto.',
                'date'     => 'El campo :attribute debe ser una fecha válida.',
                'array'    => 'Debe seleccionar al menos un estudiante.',
                'min'      => 'Debe seleccionar al menos :min estudiante.',
                'max'      => 'El campo :attribute no debe exceder :max caracteres.',
                'exists'   => 'El :attribute seleccionado no es válido.',
                'after_or_equal' => 'La :attribute no puede ser menor a la fecha de emisión.',
            ],
            [
                'idConceptoDePago'   => 'concepto de pago',
                'fechaEmisionDePago'=> 'fecha de emisión de pago',
                'fechaLimiteDePago' => 'fecha límite de pago',
                'estudiantes'       => 'estudiantes',
                'aportacion'        => 'aportación',
            ]
        );


        if ($validator->fails()) {
            return back()
                ->with('popupError', 'No se pudieron generar los pagos. Verifica la información.')
                ->withErrors($validator)
                ->withInput();
        }

        $concepto = ConceptoDePago::findOrFail($request->idConceptoDePago);
        $fechaLimitePago = Carbon::parse($request->fechaLimiteDePago);
        $fechaEmisionPago = Carbon::parse($request->fechaEmisionDePago);



        $contadorReferencias = 0;
        $referenciasCreadas = [];
        $referenciasDuplicadas = [];
        $omitidosPorPlan = [];


        try {

            // =============================
            // TRANSACCIÓN
            // =============================
            DB::transaction(function () use ($request,$concepto,$fechaLimitePago,$fechaEmisionPago,&$contadorReferencias,&$referenciasCreadas,&$referenciasDuplicadas,&$omitidosPorPlan) 
            {


                foreach ($request->estudiantes as $idEstudiante) {

                    $estudiante = Estudiante::with('usuario')->findOrFail($idEstudiante);


                    // =============================
                    // VALIDAR PLAN DE PAGO ACTIVO
                    // =============================
                    $conceptosRestringidos = [1, 2, 30];

                    if (
                        $estudiante->tienePlanActivo() &&
                        in_array($concepto->idConceptoDePago, $conceptosRestringidos)
                    ) {

                        $omitidosPorPlan[] = [
                            'estudiante' => $estudiante->usuario->primerNombre . ' ' . $estudiante->usuario->primerApellido,
                            'concepto'   => $concepto->nombreConceptoDePago,
                            'motivo'     => 'Cuenta con plan de pago activo',
                        ];

                        continue;
                    }


                    // =============================
                    // CALCULAR MONTO FINAL
                    // =============================
                    $costoFinal = $concepto->costo;

                    // ¿Es mensualidad?
                    $esMensualidad = ($concepto->idConceptoDePago == 2);

                    // =============================
                    // VALIDAR SI SE APLICA BECA
                    // =============================
                    $ignorarBeca = (
                        $fechaLimitePago->day == 15 &&
                        in_array($fechaLimitePago->month, [3, 9])
                    );

                    if ($esMensualidad && !$ignorarBeca) {

                        $solicitudBeca = $estudiante->solicitudesDeBeca()
                            ->where('idEstatus', 6) // Aprobada
                            ->with('beca')
                            ->first();

                        if ($solicitudBeca && $solicitudBeca->beca) {

                            $porcentaje = $solicitudBeca->beca->porcentajeDeDescuento;
                            $descuento  = ($concepto->costo * $porcentaje) / 100;

                            $costoFinal = $concepto->costo - $descuento;
                        }
                    }


                    // =============================
                    // GENERAR REFERENCIA
                    // =============================
                    $anioBase = 2013;
                    $anioCond = ($fechaLimitePago->year - $anioBase) * 372;
                    $mesCond  = ($fechaLimitePago->month - 1) * 31;
                    $diaCond  = ($fechaLimitePago->day - 1);
                    $fechaCondensada = $anioCond + $mesCond + $diaCond;

                    $prefijo = '0007777';
                    $matricula = $estudiante->matriculaNumerica;

                    $conceptoFormateado = str_pad(
                        $concepto->idConceptoDePago,
                        2,
                        '0',
                        STR_PAD_LEFT
                    );

                    $monto = number_format($costoFinal, 2, '', '');
                    $monto = str_pad($monto, 10, '0', STR_PAD_LEFT);

                    $ponderadores = [7, 3, 1];
                    $digitos = str_split($monto);
                    $suma = 0;

                    foreach ($digitos as $i => $digito) {
                        $indice = (count($digitos) - 1 - $i) % 3;
                        $suma += ((int)$digito) * $ponderadores[$indice];
                    }

                    $importeCondensado = $suma % 10;
                    $constante = '2';

                    $referenciaInicial =
                        $prefijo .
                        $matricula .
                        $conceptoFormateado .
                        $fechaCondensada .
                        $importeCondensado .
                        $constante;

                    $ponderadores97 = [11, 13, 17, 19, 23];
                    $digitosRef = str_split($referenciaInicial);
                    $suma = 0;

                    foreach ($digitosRef as $i => $digito) {
                        $pos = (count($digitosRef) - 1 - $i) % count($ponderadores97);
                        $suma += ((int)$digito) * $ponderadores97[$pos];
                    }

                    $remanente = str_pad(($suma % 97) + 1, 2, '0', STR_PAD_LEFT);
                    $referenciaFinal = $referenciaInicial . $remanente;

                    // =============================
                    // VERIFICAR DUPLICADO
                    // =============================
                    $pagoExistente = Pago::where('Referencia', $referenciaFinal)
                                        ->where('idEstudiante', $estudiante->idEstudiante)
                                        ->first();

                    if (!$pagoExistente) {
                        // =============================
                        // GUARDAR PAGO
                        // =============================
                        Pago::create([
                            'Referencia'            => $referenciaFinal,
                            'idEstudiante'          => $estudiante->idEstudiante,
                            'idConceptoDePago'      => $concepto->idConceptoDePago,
                            'montoAPagar'            => $costoFinal,
                            'fechaGeneracionDePago' => $fechaEmisionPago,
                            'fechaLimiteDePago'     => $fechaLimitePago,
                            'aportacion'            => $request->aportacion,
                            'idEstatus'             => 3,
                        ]);

                        $referenciasCreadas[] = [
                            'estudiante' => $estudiante->usuario->primerNombre . ' ' . $estudiante->usuario->primerApellido,
                            'referencia' => $referenciaFinal,
                            'concepto'   => $concepto->nombreConceptoDePago,
                            'fecha'      => $fechaLimitePago->format('Y-m-d'),
                        ];

                        $contadorReferencias++;

                        // =============================
                        // CREAR NOTIFICACIÓN PARA EL ESTUDIANTE
                        // =============================
                        Notificacion::create([
                            'idUsuario'         => $estudiante->idUsuario,
                            'titulo'            => 'Nuevo pago asignado',
                            'mensaje'           => "Se te ha asignado el concepto de pago: {$concepto->nombreConceptoDePago}. Revisa tus pagos.",
                            'tipoDeNotificacion'=> 1, // Informativo
                            'fechaDeInicio' => $fechaEmisionPago->toDateString(),
                            'fechaFin'           => $fechaEmisionPago->copy()->addDays(3)->toDateString(),
                            'leida'             => 0,
                        ]);
                    } else {
                        $referenciasDuplicadas[] = [
                            'estudiante' => $estudiante->usuario->primerNombre . ' ' . $estudiante->usuario->primerApellido,
                            'referencia' => $referenciaFinal,
                            'concepto'   => $concepto->nombreConceptoDePago,
                            'fecha'      => $fechaLimitePago->format('Y-m-d'),
                        ];
                    }
                }
            });
        } catch (\Throwable $e) {
            return back()->with('popupError', 'Ocurrió un error al generar los pagos.');
        }

        // =============================
        // MENSAJE FINAL
        // =============================
        $mensajePopup = "{$contadorReferencias} referencias generadas. " .
                        count($referenciasDuplicadas) . " referencias ya existían.";

        return redirect()
            ->route('pagos.detalles-referencias')
            ->with('successPagos', true)
            ->with('creados', $referenciasCreadas)
            ->with('duplicados', $referenciasDuplicadas)
            ->with('omitidos', $omitidosPorPlan);;
        
    }


    public function detallesReferencias()
    {
        return view('SGFIDMA.moduloPagos.detallesGeneracionDeReferencias', [
            'creados'    => session('creados', []),
            'duplicados' => session('duplicados', []),
            'omitidos'   => session('omitidos', []),
        ]);
    }

}
