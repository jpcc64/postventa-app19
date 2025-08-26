<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class Login extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // Auth::attempt() se encarga de todo
        if (Auth::attempt($credentials)) {
            Log::info('Usuario autenticado: ', ['usuario' => $credentials['username'], 'hora' => now()]);
            $request->session()->regenerate(); // Protege contra session fixation
            return redirect('/');
        }

        return back()->with('error', 'Credenciales incorrectas');
    }

    public function logout(Request $request)
    {
        Log::info('Usuario desconectado: ', ['usuario' => Auth::user()->username, 'hora' => now()]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
