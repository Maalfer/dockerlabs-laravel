<?php

namespace App\Http\Controllers\BunkerLabs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\BunkerToken;

class AuthController extends Controller
{
    /**
     * GET /bunkerlabs/login
     * Muestra el formulario de login por token del búnker.
     * Vista: resources/views/login-bunkerlabs.blade.php
     */
    public function showLogin()
    {
        return view('login-bunkerlabs');
    }

    /**
     * POST /bunkerlabs/login
     * Valida el token de acceso al búnker (no es el login normal de usuarios).
     * - Comprueba que el token exista (comparando con token_hash)
     * - Que esté activo y no expirado
     * - Marca la sesión como autenticada para el búnker
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'token' => ['required','string','min:10'],
        ]);

        $plain = $data['token'];

        // Buscamos tokens activos y no expirados que coincidan con el hash
        $token = BunkerToken::query()
            ->where('active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get()
            ->first(function ($t) use ($plain) {
                return Hash::check($plain, $t->token_hash);
            });

        if (!$token) {
            return back()->withErrors(['token' => 'Token inválido o expirado.']);
        }

        // Guardamos una marca en sesión para permitir el acceso al búnker
        $request->session()->put('bunkerlabs_authenticated', true);

        return redirect()->route('bunkerlabs.home')->with('status', 'Acceso concedido.');
    }
}
