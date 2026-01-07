<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    public function generarReferencia()
    {
        // =============================
        // DATOS BASE (mock / random)
        // =============================

        $prefijo = '0007777';

        // matricula numerica del estudiante
        // aquí normalmente la sacas del modelo Estudiante
        $matricula = Auth::user()->estudiante->matriculaNumerica ?? '20230001';

        // concepto de pago (ej. 1 → 01)
        $idConcepto = 1;
        $conceptoFormateado = str_pad($idConcepto, 2, '0', STR_PAD_LEFT);

        // fecha condensada (RANDOM por ahora)
        $fechaCondensada = now()->format('ymd'); // luego se cambia por excel

        // importe condensado (RANDOM)
        $importeCondensado = str_pad(rand(1000, 9999999), 7, '0', STR_PAD_LEFT);

        // constante fija
        $constante = 2;

        // remanente (2 dígitos)
        $remanente = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);

        // =============================
        // REFERENCIA FINAL
        // =============================
        $referencia = $prefijo
            . $matricula
            . $conceptoFormateado
            . $fechaCondensada
            . $importeCondensado
            . $constante
            . $remanente;

        // =============================
        // PDF
        // =============================
        $pdf = Pdf::loadView('SGFIDMA.moduloPagos.formatoReferenciaDePago', [
            'referencia' => $referencia,
            'matricula' => $matricula,
            'importe' => $importeCondensado,
            'fecha' => now()->format('d/m/Y')
        ]);

        return $pdf->download('Referencia_de_Pago.pdf');
    }
}
