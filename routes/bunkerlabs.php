<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use App\Models\BunkerToken;
use App\Http\Controllers\AuthController as CoreAuth;
use App\Models\MaquinaBunker;

Route::prefix('bunkerlabs')->as('bunkerlabs.')->group(function () {

    Route::get('/login', [CoreAuth::class, 'showLoginBunker'])->name('login');
    Route::post('/login', [CoreAuth::class, 'loginBunker'])->name('login.submit');

    Route::get('/', function (Request $request) {
        if (!$request->session()->get('bunkerlabs_authenticated')) {
            return redirect()->route('bunkerlabs.login')->withErrors([
                'token' => 'Necesitas un token válido para acceder.',
            ]);
        }

        $f = $request->query('dificultad');

        $maquinas = MaquinaBunker::query()
            ->when($f, fn ($q) => $q->difficulty($f))
            ->latest('id')
            ->get();

        return view('home-bunkerlabs', [
            'maquinas' => $maquinas,
            'f'        => $f,
        ]);
    })->name('home');

    Route::middleware([
        'auth',
        \App\Http\Middleware\RoleMiddleware::class . ':admin'
    ])->group(function () {

        Route::post('/perfil/tokens', function (Request $request) {
            $plain = Str::random(64);
            $hash  = Hash::make($plain);

            BunkerToken::create([
                'name'             => $request->input('name'),
                'token_hash'       => $hash,
                'token_ciphertext' => Crypt::encryptString($plain),
                'created_by'       => $request->user()->id,
                'active'           => true,
                'expires_at'       => $request->input('expires_at'),
            ]);

            session()->flash('bunker_token_plain', $plain);
            return back()->with('status', 'Token creado correctamente.');
        })->name('perfil.tokens.create');

        Route::get('/perfil/tokens', function () {
            $tokens = BunkerToken::orderByDesc('created_at')->get();
            return view('profile.roles-index', [
                'bunkerTokens' => $tokens,
            ]);
        })->name('perfil.tokens.index');

        Route::post('/perfil/tokens/{id}/toggle', function ($id) {
            $token = BunkerToken::findOrFail($id);
            $token->active = !$token->active;
            $token->save();
            return back();
        })->name('perfil.tokens.toggle');

        Route::delete('/perfil/tokens/{id}', function ($id) {
            BunkerToken::whereKey($id)->delete();
            return back();
        })->name('perfil.tokens.delete');
    });
});
