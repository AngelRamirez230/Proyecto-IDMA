<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Estudiante;

class LoginController extends Controller
{
    /**
     * Mostrar vista de login
     */
    public function showLogin()
    {
        return view('layouts.login'); // ajusta el nombre si tu vista es distinta
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        // 1️⃣ Validación básica
        $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string'
        ]);

        $input = $request->usuario;
        $password = $request->password;

        $usuario = null;


        /**
         * 2️⃣ Buscar en tabla Usuario
         * - correoInstitucional
         * - nombreUsuario
         */
        if (str_contains($input, '@')) {
            $usuario = Usuario::where('correoInstitucional', $input)->first();
        } else {
            $usuario = Usuario::where('nombreUsuario', $input)->first();
        }

        /**
         * 3️⃣ Si no existe, buscar como Estudiante (matrícula)
         */
        if (!$usuario) {
            $estudiante = Estudiante::where('matriculaNumerica', $input)
                ->orWhere('matriculaAlfanumerica', $input)
                ->first();

            if ($estudiante) {
                $usuario = Usuario::find($estudiante->idUsuario);
            }
        }

        /**
         * ❌ Usuario no encontrado
         */
        if (!$usuario) {
            return back()->withErrors([
                'usuario' => 'Usuario no encontrado'
            ])->withInput();
        }

        /**
         * 4️⃣ Validar estatus (solo Activo = 1)
         */
        if ($usuario->idestatus != 1) {
            return back()->withErrors([
                'usuario' => 'Usuario suspendido o dado de baja'
            ]);
        }

        /**
         * 5️⃣ Validar contraseña (bcrypt)
         */
        if (!Hash::check($password, $usuario->contraseña)) {
            return back()->withErrors([
                'password' => 'Contraseña incorrecta'
            ])->withInput();
        }

        /**
         * 6️⃣ Crear sesión
         */
        session([
            'idUsuario' => $usuario->idUsuario,
            'idTipoDeUsuario' => $usuario->idtipoDeUsuario,
            'nombreCompleto' => trim(
                $usuario->primerNombre . ' ' .
                $usuario->segundoNombre . ' ' .
                $usuario->primerApellido . ' ' .
                $usuario->segundoApellido
            )
        ]);

        /**
         * 7️⃣ Redirección según rol
         */
        return match ($usuario->idtipoDeUsuario) {
            1 => redirect()->route('inicio'),
            2 => redirect()->route('inicio'),
            3 => redirect()->route('inicio'),
            4 => redirect()->route('inicio'),
            default => redirect('/')
        };
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        session()->flush();
        return redirect()->route('login.form');
    }
}
