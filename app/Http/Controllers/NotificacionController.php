<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacion;

class NotificacionController extends Controller
{
    public function marcarComoLeida($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->leida = 1;
        $notificacion->save();

        return response()->json(['success' => true]);
    }
}

