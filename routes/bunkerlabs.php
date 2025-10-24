<?php
// routes/bunkerlabs.php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\BunkerToken;

Route::prefix('bunkerlabs')->as('bunkerlabs.')->group(function () {
    // Login por token (pantalla)
    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginBunker'])
        ->middleware('guest')
        ->name('login');

    // Login por token (submit)
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'loginBunker'])
        ->middleware('guest')
        ->name('login.submit');

    // Home BunkerLabs (usa tu validación por sesión actual)
    Route::get('/', function (Request $request) {
        if (!$request->session()->get('bunkerlabs_authenticated')) {
            return redirect()->route('bunkerlabs.login')->withErrors([
                'token' => 'Necesitas un token válido para acceder.',
            ]);
        }
        return view('home-bunkerlabs');
    })->name('home');

    // ===== Gestión de tokens (secciones del /perfil) – solo admin =====
    Route::middleware([
        'auth',
        \App\Http\Middleware\RoleMiddleware::class . ':admin'
    ])->group(function () {
        // Crear token (y exponerlo una sola vez por sesión)
        Route::post('/perfil/tokens', function (Request $request) {
            $plain = Str::random(64);
            $hash = Hash::make($plain);

            $token = BunkerToken::create([
                'token_hash' => $hash,
                'created_by' => $request->user()->id,
                'active'     => true,
            ]);

            // Guardamos el token plano en sesión para mostrarlo una vez
            session()->flash('bunkerlabs_new_token_plain', $plain);

            return back()->with('status', 'Token creado correctamente.');
        })->name('perfil.tokens.create');

        // Listado tokens
        Route::get('/perfil/tokens', function () {
            $tokens = BunkerToken::orderByDesc('created_at')->get();
            return view('profile.roles-index', [
                'bunkerTokens' => $tokens,
            ]);
        })->name('perfil.tokens.index');

        // Activar/Desactivar token
        Route::post('/perfil/tokens/{id}/toggle', function (Request $request, $id) {
            $token = BunkerToken::findOrFail($id);
            $token->active = !$token->active;
            $token->save();
            return back();
        })->name('perfil.tokens.toggle');

        // Eliminar token
        Route::delete('/perfil/tokens/{id}', function (Request $request, $id) {
            BunkerToken::whereKey($id)->delete();
            return back();
        })->name('perfil.tokens.delete');
    });
});
