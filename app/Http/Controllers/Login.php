<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // Auth::attempt() se encarga de todo
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Protege contra session fixation
            return redirect('/');
        }

        return back()->with('error', 'Credenciales incorrectas');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
