<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
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
        // NOMBRE COMPLETO
        // =============================
        $nombreCompleto = trim(
            $usuario->primerNombre . ' ' .
            $usuario->segundoNombre . ' ' .
            $usuario->primerApellido . ' ' .
            $usuario->segundoApellido
        );

        // =============================
        // FECHA LÍMITE DE PAGO (DÍA 15)
        // =============================
        $fechaLimitePago = Carbon::now()->day(15);

        if (Carbon::now()->day > 15) {
            $fechaLimitePago->addMonth();
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
        $monto = number_format($concepto->costo, 2, '', ''); // ej. 1130.00 → 113000
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
        // GUARDAR PAGO
        // =============================
        Pago::create([
            'Referencia'             => $referenciaFinal,
            'idConceptoDePago'       => $concepto->idConceptoDePago,
            'fechaGeneracionDePago'  => now(),
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
            ]
        )->setPaper('letter');

        return $pdf->download('Referencia_de_Pago.pdf');
    }
}
