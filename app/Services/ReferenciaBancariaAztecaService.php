<?php

namespace App\Services;

use App\Models\Estudiante;
use App\Models\ConceptoDePago;
use Carbon\Carbon;

class ReferenciaBancariaAztecaService
{
    public static function generar(
        Estudiante $estudiante,
        ConceptoDePago $concepto,
        float $monto,
        Carbon $fechaLimite
    ): string {

        $anioBase = 2013;
        $anioCond = ($fechaLimite->year - $anioBase) * 372;
        $mesCond  = ($fechaLimite->month - 1) * 31;
        $diaCond  = ($fechaLimite->day - 1);

        $fechaCondensada = $anioCond + $mesCond + $diaCond;

        $prefijo   = '0007777';
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
        $monto = number_format($monto, 2, '', '');
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

        // =============================
        // MOD 97
        // =============================
        $ponderadores97 = [11, 13, 17, 19, 23];
        $digitosRef = str_split($referenciaInicial);
        $suma = 0;

        foreach ($digitosRef as $i => $digito) {
            $pos = (count($digitosRef) - 1 - $i) % count($ponderadores97);
            $suma += ((int)$digito) * $ponderadores97[$pos];
        }

        $remanente = str_pad(($suma % 97) + 1, 2, '0', STR_PAD_LEFT);

        return $referenciaInicial . $remanente;
    }
}
