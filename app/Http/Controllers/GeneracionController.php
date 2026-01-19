<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Generacion;
use App\Models\Mes;

class GeneracionController extends Controller
{
    public function verificarGeneracion()
    {
        if (!Auth::user()->esAdmin()) {
            return null;
        }
        $mesActual = now()->month;
        $añoActual = now()->year;

        // ===============================
        // MOSTRAR AVISO UN MES ANTES
        // ===============================
        if ($mesActual == 2) {
            $mesInicioReal = 3;
        } elseif ($mesActual == 8) {
            $mesInicioReal = 9;
        } else {
            return null;
        }

        // ===============================
        // VERIFICAR SI YA EXISTE
        // ===============================
        $existe = Generacion::where('añoDeInicio', $añoActual)
            ->where('idMesInicio', $mesInicioReal)
            ->exists();

        if ($existe) {
            return null;
        }

        // ===============================
        // RETORNAR DATOS SUGERIDOS
        // ===============================
        return $this->armarDatosGeneracion($añoActual, $mesInicioReal);
    }


    /**
     * Crear generación desde el dashboard (con confirmación implícita)
     */
    public function crearDesdeDashboard(Request $request)
    {
        // 🔐 Blindaje contra duplicados
        $duplicado = Generacion::where('añoDeInicio', $request->añoDeInicio)
            ->where('idMesInicio', $request->idMesInicio)
            ->exists();

        if ($duplicado) {
            return redirect()->back()
                ->with('popupError', 'La generación ya existe');
        }

        Generacion::create([
            'añoDeInicio'       => $request->añoDeInicio,
            'idMesInicio'      => $request->idMesInicio,
            'añoDeFinalizacion'          => $request->añoDeFinalizacion,
            'idMesFin'         => $request->idMesFin,
            'nombreGeneracion' => $request->nombreGeneracion,
            'idEstatus'  => 1, // Activa
        ]);

        return redirect()->back()
            ->with('success', 'Generación creada correctamente');
    }

    /**
     * 🧾 ARMA EL NOMBRE DE LA GENERACIÓN
     * Ejemplos:
     *  - Septiembre 21 - Agosto 25
     *  - Marzo 24 - Febrero 28
     */
    public function armarDatosGeneracion(int $añoDeInicio, int $mesInicio): array
    {
        if ($mesInicio == 3) {
            // Marzo → Febrero +4 años
            $mesFin  = 2;
            $añoDeFinalizacion = $añoDeInicio + 4;

            $nombreGeneracion =
                'Marzo ' . substr($añoDeInicio, -2) .
                ' - Febrero ' . substr($añoDeFinalizacion, -2);
        } else {
            // Septiembre → Agosto +4 años
            $mesFin  = 8;
            $añoDeFinalizacion = $añoDeInicio + 4;

            $nombreGeneracion =
                'Septiembre ' . substr($añoDeInicio, -2) .
                ' - Agosto ' . substr($añoDeFinalizacion, -2);
        }

        return [
            'añoDeInicio'       => $añoDeInicio,
            'idMesInicio'      => $mesInicio,
            'añoDeFinalizacion'  => $añoDeFinalizacion,
            'idMesFin'         => $mesFin,
            'nombreGeneracion' => $nombreGeneracion,
        ];
    }
}
