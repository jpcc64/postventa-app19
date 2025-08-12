<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'username' => 'required|unique:users|max:255',
            'password' => 'required|min:6'
        ]);

        $user = new User();
        $user->username = $validatedData['username'];
        $user->password = bcrypt($validatedData['password']);
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Usuario creado con éxito');
    }
}
