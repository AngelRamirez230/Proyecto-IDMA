<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

// MODELOS
use App\Models\Pago;
use App\Models\ConceptoDePago;

class PagoController extends Controller
{
    public function generarReferencia($idConcepto)
    {
        // =============================
        // ESTUDIANTE Y USUARIO
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

        $fechaCondensada = now()->format('ymd');
        $importeCondensado = str_pad(rand(1000, 9999), 6, '0', STR_PAD_LEFT);
        $constante = '2';
        $remanente = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);

        $referencia = $prefijo
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
            'Referencia' => $referencia,
            'idConceptoDePago' => $concepto->idConceptoDePago,
            'ImporteDePago' => $concepto->costo,
            'fechaGeneracionDePago' => now(),
            'idEstatus' => 3,
            'idEstudiante' => $estudiante->idEstudiante,
        ]);

        // =============================
        // PDF
        // =============================
        $pdf = Pdf::loadView(
            'SGFIDMA.moduloPagos.formatoReferenciaDePago',
            [
                'referencia'      => $referencia,
                'estudiante'      => $estudiante,
                'concepto'        => $concepto,
                'nombreCompleto'  => $nombreCompleto,
                'fechaEmision'    => now()->format('d/m/Y'),
                'fechaLimite'     => now()->addDays(12)->format('d/m/Y'),
            ]
        )->setPaper('letter');

        return $pdf->download('Referencia_de_Pago.pdf');
    }
}
