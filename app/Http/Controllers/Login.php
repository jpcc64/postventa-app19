<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\AccionUsuarioRegistrada;


class Login extends Controller
{
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        $terminal_requerida = $request->input('terminal'); 

        if ($username === 'admin') {
            $terminal_requerida = 'admin';
        }

        $credentials = [
            'username' => $username,
            'password' => $password,
            'terminal' => $terminal_requerida,
        ];

        // Auth::attempt() se encarga de todo
        if (Auth::attempt($credentials)) {
            AccionUsuarioRegistrada::dispatch(Auth::user(), 'Inicio de sesión');
            $request->session()->regenerate(); // Protege contra session fixation
            return redirect('/');
        }
        return redirect('/login')->with('error', 'Credenciales inválidas');
    }

    public function logout(Request $request)
    {
        AccionUsuarioRegistrada::dispatch(Auth::user(), 'Cierre de sesión');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
