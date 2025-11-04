<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\WriteupTemporalController;
use App\Http\Controllers\Admin\WriteupTemporalAdminController;
use App\Http\Controllers\Admin\WriteupAdminController;
use App\Http\Controllers\EnviarMaquinaController;
use App\Http\Controllers\Admin\MaquinaRecibidaController;
use App\Http\Controllers\MisWriteupsController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Admin\TokenAdminController;
use App\Http\Controllers\MaquinaEdicionController;
use App\Http\Controllers\Admin\MaquinaEdicionAdminController;
use App\Http\Controllers\Admin\MaquinaBunkerAdminController;

Route::get('/media/{path}', function (string $path) {
    $relative = ltrim($path, '/');
    $relative = preg_replace('#^(storage/|app/public/)#', '', $relative);
    $full = storage_path('app/public/' . $relative);
    if (!is_file($full)) {
        abort(404);
    }
    $mime = File::mimeType($full) ?: 'application/octet-stream';
    return response()->file($full, [
        'Content-Type'  => $mime,
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->where('path', '.*')->name('media');

Route::get('/storage/{path}', function (string $path) {
    $relative = ltrim($path, '/');
    $relative = preg_replace('#^(storage/|app/public/)#', '', $relative);
    $full = storage_path('app/public/' . $relative);
    if (!is_file($full)) {
        abort(404);
    }
    $mime = File::mimeType($full) ?: 'application/octet-stream';
    return response()->file($full, [
        'Content-Type'  => $mime,
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->where('path', '.*')->name('storage.proxy');

Route::as('dockerlabs.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/perfil', [ProfileController::class, 'update'])->name('profile.update');

        Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
            Route::get('/perfil/roles', [ProfileController::class, 'rolesIndex'])->name('profile.roles.index');
            Route::patch('/perfil/roles/{user}', [ProfileController::class, 'rolesUpdate'])->name('profile.roles.update');
            Route::delete('/perfil/roles/{user}', [ProfileController::class, 'destroyUser'])->name('profile.roles.destroyUser');
        });
    });

    Route::get('/enviar-maquina', [EnviarMaquinaController::class, 'create'])->name('enviar-maquina.form');
    Route::post('/enviar-maquina', [EnviarMaquinaController::class, 'store'])->name('enviar-maquina.store');

    Route::middleware('auth')->group(function () {
        Route::get('/mis-writeups', [MisWriteupsController::class, 'index'])->name('mis-writeups.index');
        Route::post('/mis-writeups/{id}/solicitar-cambio', [MisWriteupsController::class, 'solicitarCambio'])->name('mis-writeups.solicitar-cambio');
        Route::get('/mis-maquinas', [ProfileController::class, 'misMaquinas'])->name('mis-maquinas');
        Route::post('/mis-maquinas/{id}/solicitar-edicion', [MaquinaEdicionController::class, 'store'])->name('mis-maquinas.solicitar-edicion');
    });

    Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

        Route::get('/admin/maquinas-recibidas', [MaquinaRecibidaController::class, 'index'])->name('admin.maquinas.recibidas');
        Route::post('/admin/maquinas-recibidas/{id}/prefill', [MaquinaRecibidaController::class, 'prefill'])->name('admin.maquinas.recibidas.prefill');

        Route::get('/admin/writeups-temporal', [WriteupTemporalAdminController::class, 'index'])->name('admin.writeups-temporal.index');
        Route::post('/admin/writeups-temporal/{id}/approve', [WriteupTemporalAdminController::class, 'approve'])->name('admin.writeups-temporal.approve');
        Route::delete('/admin/writeups-temporal/{id}', [WriteupTemporalAdminController::class, 'destroy'])->name('admin.writeups-temporal.destroy');

        Route::get('/admin/writeups', [WriteupAdminController::class, 'index'])->name('admin.writeups.index');
        Route::delete('/admin/writeups/{id}', [WriteupAdminController::class, 'destroy'])->name('admin.writeups.destroy');

        Route::get('/admin/maquinas-editadas', [MaquinaEdicionAdminController::class, 'index'])->name('admin.maquinas-editadas.index');
        Route::post('/admin/maquinas-editadas/{edicion}/approve', [MaquinaEdicionAdminController::class, 'approve'])->name('admin.maquinas-editadas.approve');
        Route::delete('/admin/maquinas-editadas/{edicion}', [MaquinaEdicionAdminController::class, 'destroy'])->name('admin.maquinas-editadas.destroy');
    });

    Route::post('/writeups-temporal', [WriteupTemporalController::class, 'store'])->name('writeups-temporal.store');
});

Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    Route::post('/admin/maquinas', [MaquinaController::class, 'store'])->name('admin.maquinas.store');
    Route::delete('/admin/maquinas/{maquina}', [MaquinaController::class, 'destroy'])->name('admin.maquinas.destroy');

    Route::get('/admin/bunkerlabs/tokens', [TokenAdminController::class, 'index'])->name('admin.bunker.tokens.index');
    Route::post('/admin/bunkerlabs/tokens', [TokenAdminController::class, 'store'])->name('admin.bunker.tokens.store');
    Route::post('/admin/bunkerlabs/tokens/{id}/toggle', [TokenAdminController::class, 'toggle']);
    Route::delete('/admin/bunkerlabs/tokens/{id}', [TokenAdminController::class, 'destroy']);

    Route::delete('/admin/bunkerlabs/maquinas/{maquina}', [MaquinaBunkerAdminController::class, 'destroy'])->name('admin.bunker.maquinas.destroy');
});
