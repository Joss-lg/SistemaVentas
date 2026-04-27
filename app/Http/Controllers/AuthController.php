<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class AuthController extends Controller
{
    // Mostrar el formulario de login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Procesar el inicio de sesión
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Intentar autenticar (Laravel buscará en el modelo Usuario por defecto si lo configuraste)
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            $user = Auth::user();

            // LÓGICA DE REDIRECCIÓN POR ROL
            if ($user->rol === 'administrador') {
                return redirect()->intended('/admin/dashboard');
            }

            // Si es cajero o supervisor, va a ventas
            return redirect()->intended('/ventas');
        }

        return back()->withErrors([
            'username' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('username');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    public function logoutEspecial(Request $request)
    {
        
        Auth::guard('web')->logout();

        
        $request->session()->flush();

        
        $request->session()->invalidate();

        // 4. Regenerar el token CSRF
        $request->session()->regenerateToken();

        
        return redirect('/login')
            ->with('corte_exito', 'Sesión cerrada correctamente')
            ->withCookie(cookie()->forget('laravel_session')); 
            }
}