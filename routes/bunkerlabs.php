<?php
// routes/bunkerlabs.php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\BunkerToken;

// Usa tu controlador existente (no hay namespace BunkerLabs\)
use App\Http\Controllers\AuthController as CoreAuth;

Route::prefix('bunkerlabs')->as('bunkerlabs.')->group(function () {
    // Pantalla de login del b�nker (token)
    Route::get('/login', [CoreAuth::class, 'showLoginBunker'])
        ->middleware('guest')
        ->name('login');

    // Submit del login (token)
    Route::post('/login', [CoreAuth::class, 'loginBunker'])
        ->middleware('guest')
        ->name('login.submit');

    // Home del b�nker (si ya est� autenticado con token en sesi�n)
    Route::get('/', function (Request $request) {
        if (!$request->session()->get('bunkerlabs_authenticated')) {
            return redirect()->route('bunkerlabs.login')->withErrors([
                'token' => 'Necesitas un token v�lido para acceder.',
            ]);
        }
        return view('home-bunkerlabs');
    })->name('home');

    // ===== Gesti�n de tokens (solo admin) =====
    Route::middleware([
        'auth',
        \App\Http\Middleware\RoleMiddleware::class . ':admin'
    ])->group(function () {
        // Crear token
        Route::post('/perfil/tokens', function (Request $request) {
            $plain = Str::random(64);
            $hash  = Hash::make($plain);

            BunkerToken::create([
                'name'       => $request->input('name'),
                'token_hash' => $hash,
                'created_by' => $request->user()->id,
                'active'     => true,
                'expires_at' => $request->input('expires_at'),
            ]);

            session()->flash('bunker_token_plain', $plain);
            return back()->with('status', 'Token creado correctamente.');
        })->name('perfil.tokens.create');

        // Listado tokens (usa tu vista actual)
        Route::get('/perfil/tokens', function () {
            $tokens = BunkerToken::orderByDesc('created_at')->get();
            return view('profile.roles-index', [
                'bunkerTokens' => $tokens,
            ]);
        })->name('perfil.tokens.index');

        // Activar/Desactivar
        Route::post('/perfil/tokens/{id}/toggle', function ($id) {
            $token = BunkerToken::findOrFail($id);
            $token->active = !$token->active;
            $token->save();
            return back();
        })->name('perfil.tokens.toggle');

        // Eliminar
        Route::delete('/perfil/tokens/{id}', function ($id) {
            BunkerToken::whereKey($id)->delete();
            return back();
        })->name('perfil.tokens.delete');
    });
});
