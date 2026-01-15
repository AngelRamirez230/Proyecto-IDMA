<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

// MODELOS
use App\Models\Pago;
use App\Models\Estudiante;
use App\Models\ConceptoDePago;

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
        $request->validate([
            'idConceptoDePago' => 'required',
            'fechaLimiteDePago'=> 'required|date',
            'estudiantes'      => 'required|array|min:1',
        ]);

        $concepto = ConceptoDePago::findOrFail($request->idConceptoDePago);
        $fechaLimitePago = Carbon::parse($request->fechaLimiteDePago);

        foreach ($request->estudiantes as $idEstudiante) {

            $estudiante = Estudiante::findOrFail($idEstudiante);

            // =============================
            // 🔥 GENERAR REFERENCIA (TU LÓGICA)
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

            $monto = number_format($concepto->costo, 2, '', '');
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
            // GUARDAR PAGO
            // =============================
            Pago::create([
                'Referencia'            => $referenciaFinal,
                'idEstudiante'          => $estudiante->idEstudiante,
                'idConceptoDePago'      => $concepto->idConceptoDePago,
                'fechaGeneracionDePago' => now(),
                'fechaLimiteDePago'     => $fechaLimitePago,
                'idEstatus'             => 3,
            ]);
        }

        return redirect()
            ->route('admin.pagos.create')
            ->with('success', 'Pagos generados correctamente');
    }




}
