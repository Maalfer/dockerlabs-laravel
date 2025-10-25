<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BunkerToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // GET /login
    public function showLogin(Request $request)
    {
        if ($request->session()->get('bunkerlabs_authenticated')) {
            return redirect()->route('bunkerlabs.home');
        }
    
        return view('login');
    }


    // POST /login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/perfil');
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas.',
        ])->withInput();
    }

    // ====== Bunkerlabs por token ======

    // GET /login-bunkerlabs
    public function showLoginBunker()
    {
        // Si ya tengo acceso al búnker, voy directo al home del búnker
        if (session('bunkerlabs_authenticated')) {
            return redirect()->route('bunkerlabs.home');
        }

        return view('login-bunkerlabs');
    }

    // POST /login-bunkerlabs (token-only, permanente)
    public function loginBunker(Request $request)
    {
        $data = $request->validate([
            'token' => ['required','string','min:8'],
        ]);

        $plain = trim($data['token']);

        // Buscar cualquier token activo y comprobar por hash (tokens permanentes)
        $token = BunkerToken::query()
            ->where('active', true)
            ->get()
            ->first(function ($t) use ($plain) {
                return Hash::check($plain, $t->token_hash);
            });

        if (!$token) {
            return back()->withErrors(['token' => 'Token inv�lido.'])->withInput();
        }

        // Importante: regenerar la sesi�n ANTES de poner la bandera
        $request->session()->regenerate();
        $request->session()->put('bunkerlabs_authenticated', true);

        return redirect()->route('bunkerlabs.home');
    }

    // ====== Fin Bunkerlabs ======

    // GET /registro
    public function showRegister()
    {
        return view('registro');
    }

    // POST /registro
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required','string','max:255'],
            'email'                 => ['required','email','max:255','unique:users,email'],
            'password'              => ['required','string','min:6','confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    // POST /logout
    public function logout(Request $request)
    {
        // Limpia tambi�n la sesi�n de Bunkerlabs
        $request->session()->forget('bunkerlabs_authenticated');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
