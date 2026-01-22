<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

// MODELOS
use App\Models\Pago;
use App\Models\ConceptoDePago;

class PagoController extends Controller
{
    public function generarReferencia($idConcepto)
    {
        // =============================
        // USUARIO Y ESTUDIANTE
        // =============================
        $usuario = Auth::user();
        $estudiante = $usuario->estudiante;

        if (!$estudiante) {
            abort(403, 'No se encontró información del estudiante.');
        }

        // =============================
        // CONCEPTO DE PAGO
        // =============================
        $concepto = ConceptoDePago::findOrFail($idConcepto);

        // =============================
        // VALIDAR SI ES COLEGIATURA
        // =============================
        $esMensualidad = ($concepto->idConceptoDePago == 2);

        // =============================
        // BUSCAR BECA APROBADA
        // =============================
        $solicitudBeca = $estudiante->solicitudesDeBeca()
            ->where('idEstatus', 6) // Aprobada
            ->with('beca')
            ->first();

        // =============================
        // CALCULAR COSTO FINAL
        // =============================
        $costoFinal = $concepto->costo;

        if ($esMensualidad && $solicitudBeca && $solicitudBeca->beca) {

            $porcentaje = $solicitudBeca->beca->porcentajeDeDescuento;

            $descuento = ($concepto->costo * $porcentaje) / 100;

            $costoFinal = $concepto->costo - $descuento;
        }


        // =============================
        // NOMBRE COMPLETO
        // =============================
        $nombreCompleto = trim(
            $usuario->primerNombre . ' ' .
            $usuario->segundoNombre . ' ' .
            $usuario->primerApellido . ' ' .
            $usuario->segundoApellido
        );

        // =============================
        // FECHA LÍMITE DE PAGO
        // Regla: +8 días sin pasar de mes
        // =============================

        $fechaGeneracion = Carbon::today(); 

        $fechaLimitePago = $fechaGeneracion->copy()->addDays(8);

        // Si se pasó al siguiente mes → último día del mes original
        if ($fechaLimitePago->month !== $fechaGeneracion->month) {
            $fechaLimitePago = $fechaGeneracion->copy()->endOfMonth();
        }



        // =============================
        // FECHA CONDENSADA
        // Fórmula bancaria con base 2013
        // =============================
        $anioBase = 2013;

        $anioCond = ($fechaLimitePago->year - $anioBase) * 372;
        $mesCond  = ($fechaLimitePago->month - 1) * 31;
        $diaCond  = ($fechaLimitePago->day - 1); // siempre 14

        $fechaCondensada = $anioCond + $mesCond + $diaCond;

        // =============================
        // REFERENCIA BANCARIA
        // =============================
        $prefijo = '0007777';
        $matricula = $estudiante->matriculaNumerica;

        $conceptoFormateado = str_pad(
            $concepto->idConceptoDePago,
            2,
            '0',
            STR_PAD_LEFT
        );

        // =============================
        // IMPORTE CONDENSADO 
        // =============================

        // 1. Obtener costo y dejarlo en 10 dígitos sin punto decimal
        $monto = number_format($costoFinal, 2, '', ''); // ej. 1130.00 → 113000
        $monto = str_pad($monto, 10, '0', STR_PAD_LEFT);

        // 2. Ponderadores (se asignan desde la derecha)
        $ponderadores = [7, 3, 1];

        // 3. Convertir monto a arreglo de dígitos
        $digitos = str_split($monto);

        $suma = 0;
        $totalDigitos = count($digitos);

        // 4. Recorrer dígitos de izquierda a derecha
        foreach ($digitos as $i => $digito) {
            // Índice del ponderador desde la derecha
            $indicePonderador = ($totalDigitos - 1 - $i) % 3;
            $ponderador = $ponderadores[$indicePonderador];

            $suma += ((int)$digito) * $ponderador;
        }

        // 5. Remanente
        $importeCondensado = $suma % 10;

        $constante = '2';

        $referenciaInicial = $prefijo
            . $matricula
            . $conceptoFormateado
            . $fechaCondensada
            . $importeCondensado
            . $constante;

        // =============================
        // REMANENTE (MOD 97)
        // =============================

        // Ponderadores en ORDEN LÓGICO
        // Se asignan desde la derecha
        $ponderadores = [11, 13, 17, 19, 23];

        $digitosRef = str_split($referenciaInicial);
        $totalDigitos = count($digitosRef);
        $totalPonderadores = count($ponderadores);

        $suma = 0;

        foreach ($digitosRef as $i => $digito) {

            // posición desde la derecha (0,1,2,...)
            $posDesdeDerecha = $totalDigitos - 1 - $i;

            // ciclo correcto 11,13,17,19,23
            $indicePonderador = $posDesdeDerecha % $totalPonderadores;
            $ponderador = $ponderadores[$indicePonderador];

            $suma += ((int)$digito) * $ponderador;
        }

        $remanenteCalculado = ($suma % 97) + 1;
        $remanente = str_pad($remanenteCalculado, 2, '0', STR_PAD_LEFT);





        // =============================
        // REFERENCIA FINAL
        // =============================
        $referenciaFinal = $prefijo
            . $matricula
            . $conceptoFormateado
            . $fechaCondensada
            . $importeCondensado
            . $constante
            . $remanente;

        // =============================
        // VALIDAR SI LA REFERENCIA YA EXISTE
        // =============================
        $existeReferencia = Pago::where('Referencia', $referenciaFinal)->exists();

        if ($existeReferencia) {
            return redirect()
                ->back()
                ->with('popupError', 'La referencia de pago ya existe. Revisa tu apartado de pagos.');
        }


        // =============================
        // GUARDAR PAGO
        // =============================
        Pago::create([
            'Referencia'             => $referenciaFinal,
            'idConceptoDePago'       => $concepto->idConceptoDePago,
            'montoAPagar'            => $costoFinal,
            'fechaGeneracionDePago'  => now(),
            'fechaLimiteDePago'     => $fechaLimitePago,
            'aportacion'            => null,
            'idEstatus'              => 3, // Pendiente
            'idEstudiante'           => $estudiante->idEstudiante,
        ]);

        // =============================
        // PDF
        // =============================
        $pdf = Pdf::loadView(
            'SGFIDMA.moduloPagos.formatoReferenciaDePago',
            [
                'referencia'     => $referenciaFinal,
                'estudiante'     => $estudiante,
                'concepto'       => $concepto,
                'nombreCompleto' => $nombreCompleto,
                'fechaEmision'   => now()->format('d/m/Y'),
                'fechaLimite'    => $fechaLimitePago->format('d/m/Y'),
                'montoAPagar' => $costoFinal,
                
            ]
        )->setPaper('letter');

        return $pdf->download('Referencia_de_Pago.pdf');
    }


    public function descargarRecibo($referencia)
    {
        $pago = Pago::with([
            'estudiante.usuario',
            'concepto'
        ])->findOrFail($referencia); // 👈 usa la PK real

        $usuario = $pago->estudiante->usuario;

        $nombreCompleto = trim(
            $usuario->primerNombre . ' ' .
            $usuario->segundoNombre . ' ' .
            $usuario->primerApellido . ' ' .
            $usuario->segundoApellido
        );

        $pdf = Pdf::loadView(
            'SGFIDMA.moduloPagos.formatoReferenciaDePago',
            [
                'referencia'     => $pago->Referencia,
                'estudiante'     => $pago->estudiante,
                'concepto'       => $pago->concepto,
                'nombreCompleto' => $nombreCompleto,
                'fechaEmision'   => $pago->fechaGeneracionDePago?->format('d/m/Y'),
                'fechaLimite'    => $pago->fechaLimiteDePago?->format('d/m/Y'),
                'montoAPagar'    => $pago->montoAPagar,
                'pago'           => $pago,
            ]
        )->setPaper('letter');

        return $pdf->download(
            'Recibo_Pago_' . $pago->Referencia . '.pdf'
        );
    }


    // =============================
    // CONSULTA DE PAGOS
    // =============================
    public function index(Request $request)
    {
        $orden  = $request->orden;
        $filtro = $request->filtro;
        $buscar = $request->buscarPago;

        $query = Pago::with([
            'estudiante.usuario',
            'concepto',
            'estatus'
        ]);

        $usuario = Auth::user();

        // =============================
        // RESTRICCIÓN POR ROL
        // =============================
        if ($usuario->estudiante) {
            $query->where('idEstudiante', $usuario->estudiante->idEstudiante)
                ->whereDate('fechaGeneracionDePago', '<=', Carbon::today());
        }

        // =============================
        // BUSCADOR
        // =============================
        if ($request->filled('buscarPago')) {

            $buscar = trim($buscar);

            $query->where(function ($q) use ($buscar) {

                // Referencia de pago
                $q->where('Referencia', 'LIKE', "%{$buscar}%")

                
                ->orWhereHas('estudiante.usuario', function ($u) use ($buscar) {

                    
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
            });
        }

        // =============================
        // FILTRO
        // =============================
        if ($filtro === 'pendientes') {
            $query->where('idEstatus', 3);
        } elseif ($filtro === 'aprobados') {
            $query->where('idEstatus', 6);
        }elseif ($filtro === 'rechazados') {
            $query->where('idEstatus', 7);
        }

        // =============================
        // ORDEN
        // =============================
        if ($orden === 'alfabetico') {
            $query->orderBy('idEstudiante');
        } elseif ($orden === 'porcentaje_mayor') {
            $query->orderBy('fechaGeneracionDePago', 'desc');
        } elseif ($orden === 'porcentaje_menor') {
            $query->orderBy('fechaGeneracionDePago', 'asc');
        }

        $pagos = $query->paginate(10)->withQueryString();

        return view(
            'SGFIDMA.moduloPagos.consultaDePagos',
            compact('pagos', 'orden', 'filtro', 'buscar')
        );
    }

    public function show($referencia)
    {
        $pago = Pago::with([
            'estudiante.usuario',
            'concepto',
            'estatus'
        ])->findOrFail($referencia);

        return view('SGFIDMA.moduloPagos.detallesDePago', [
            'pago' => $pago
        ]);
    }


}
