<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\WriteupTemporalController;
use App\Http\Controllers\Admin\WriteupTemporalAdminController;
use App\Http\Controllers\Admin\WriteupAdminController;
use App\Http\Controllers\EnviarMaquinaController;
use App\Http\Controllers\Admin\MaquinaRecibidaController;
use App\Http\Controllers\MisWriteupsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleManagementController;

// ===== Añadidos para gestión de tokens Bunkerlabs =====
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\BunkerToken;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/registro', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/perfil/roles', [RoleManagementController::class, 'index'])
        ->name('profile.roles.index')
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin');

    Route::patch('/perfil/roles/{user}', [RoleManagementController::class, 'update'])
        ->name('profile.roles.update')
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin');

    Route::delete('/perfil/roles/{user}', [RoleManagementController::class, 'destroyUser'])
        ->name('profile.roles.destroyUser')
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin');
});

Route::get('/enviar-maquina', [EnviarMaquinaController::class, 'create'])->name('enviar-maquina.form');
Route::post('/enviar-maquina', [EnviarMaquinaController::class, 'store'])->name('enviar-maquina.store');

Route::middleware('auth')->group(function () {
    Route::get('/mis-writeups', [MisWriteupsController::class, 'index'])->name('mis-writeups.index');
    Route::post('/mis-writeups/{writeup}/solicitar-cambio', [MisWriteupsController::class, 'solicitarCambio'])->name('mis-writeups.solicitar-cambio');
});

Route::middleware([
    'auth',
    \App\Http\Middleware\RoleMiddleware::class . ':admin,moderator'
])->group(function () {
    Route::get('/admin/writeups-temporal', [WriteupTemporalAdminController::class, 'index'])->name('admin.writeups-temporal.index');
    Route::post('/admin/writeups-temporal/{id}/approve', [WriteupTemporalAdminController::class, 'approve'])->name('admin.writeups-temporal.approve');
    Route::delete('/admin/writeups-temporal/{id}', [WriteupTemporalAdminController::class, 'destroy'])->name('admin.writeups-temporal.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/maquinas-recibidas', [MaquinaRecibidaController::class, 'index'])->name('maquinas.recibidas');
    });
});

Route::middleware([
    'auth',
    \App\Http\Middleware\RoleMiddleware::class . ':admin'
])->group(function () {
    Route::get('admin', [MaquinaController::class, 'index'])->name('admin');
    Route::post('admin/maquinas', [MaquinaController::class, 'store'])->name('admin.maquinas.store');
    Route::delete('admin/maquinas/{maquina}', [MaquinaController::class, 'destroy'])->name('admin.maquinas.destroy');

    Route::delete('/admin/writeups/{writeup}', [WriteupAdminController::class, 'destroy'])->name('admin.writeups.destroy');

    Route::post('/admin/maquinas-recibidas/{id}/prefill', [MaquinaRecibidaController::class, 'prefill'])->name('admin.maquinas.recibidas.prefill');
});

Route::get('/admin/writeups', [WriteupAdminController::class, 'index'])
    ->name('admin.writeups.index')
    ->middleware([
        'auth',
        \App\Http\Middleware\RoleMiddleware::class . ':admin,moderator',
    ]);

Route::post('/writeups-temporal', [WriteupTemporalController::class, 'store'])->name('writeups-temporal.store');

// ===== Bunkerlabs: Login por token =====
Route::get('/login-bunkerlabs', [AuthController::class, 'showLoginBunker'])
    ->middleware('guest')
    ->name('login.bunkerlabs');

Route::post('/login-bunkerlabs', [AuthController::class, 'loginBunker'])
    ->middleware('guest')
    ->name('login.bunkerlabs.submit');

// Home Bunkerlabs con verificación inline (sin middleware ni Kernel.php)
Route::get('/home-bunkerlabs', function (Request $request) {
    if (!$request->session()->get('bunkerlabs_authenticated')) {
        return redirect()->route('login.bunkerlabs')->withErrors([
            'token' => 'Necesitas un token válido para acceder.',
        ]);
    }
    return view('home-bunkerlabs');
})->name('home.bunkerlabs');

// ===== Bunkerlabs: Gestión de tokens desde /perfil (solo admin) =====
Route::middleware([
    'auth',
    \App\Http\Middleware\RoleMiddleware::class . ':admin'
])->group(function () {
    // Crear token: devuelve el token plano en la sesión para mostrarlo una vez
    Route::post('/perfil/bunkerlabs/tokens', function (Request $request) {
        $data = $request->validate([
            'name'       => ['nullable','string','max:120'],
            // Tokens permanentes: expires_at opcional y no se usa para validar
            'expires_at' => ['nullable','date'],
        ]);

        $plain = Str::random(40);

        BunkerToken::create([
            'created_by' => $request->user()->id,
            'name'       => $data['name'] ?? null,
            'token_hash' => Hash::make($plain),
            'expires_at' => $data['expires_at'] ?? null, // ignorado si los quieres permanentes
            'active'     => true,
        ]);

        return back()->with('bunker_token_plain', $plain);
    })->name('perfil.bunker.tokens.create');

    // Activar/Desactivar un token
    Route::post('/perfil/bunkerlabs/tokens/{id}/toggle', function (Request $request, $id) {
        $token = BunkerToken::findOrFail($id);
        $token->active = !$token->active;
        $token->save();
        return back();
    })->name('perfil.bunker.tokens.toggle');

    // Eliminar un token
    Route::delete('/perfil/bunkerlabs/tokens/{id}', function (Request $request, $id) {
        BunkerToken::whereKey($id)->delete();
        return back();
    })->name('perfil.bunker.tokens.delete');
});
