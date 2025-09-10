<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario; // tabla iusuarios

class AuthController extends Controller
{
public function form()
{
    // auth.blade.php
    return view('auth');
}

    public function login(Request $request)
    {
        $cred = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ], [], [
            'email'    => 'Email',
            'password' => 'Contraseña',
        ]);

        // buscar usuario 
        $user = Usuario::where('email', $cred['email'])->first();

        // validar contraseña con bcript
        if (!$user || !Hash::check($cred['password'], $user->password)) {
            return back()
                ->withErrors(['password' => 'Credenciales inválidas'])
                ->onlyInput('email');
        }

        // autenticar y regenerar sesión
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
