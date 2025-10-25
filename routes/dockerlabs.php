<?php
// routes/dockerlabs.php

use Illuminate\Support\Facades\Route;

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

/**
 * Todas las rutas principales del proyecto DockerLabs.
 * 
 * Prefijo de nombres: dockerlabs.
 * Ejemplo: route('dockerlabs.home')
 */
Route::as('dockerlabs.')->group(function () {
    // ============================
    // HOME
    // ============================
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // ============================
    // AUTENTICACI�N
    // ============================
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');

        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    });

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    // ============================
    // PERFIL Y GESTI�N DE ROLES
    // ============================
    Route::middleware('auth')->group(function () {
        Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/perfil', [ProfileController::class, 'update'])->name('profile.update');

        Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
            Route::get('/perfil/roles', [ProfileController::class, 'rolesIndex'])->name('profile.roles.index');
            Route::patch('/perfil/roles/{user}', [ProfileController::class, 'rolesUpdate'])->name('profile.roles.update');
            Route::delete('/perfil/roles/{user}', [ProfileController::class, 'destroyUser'])->name('profile.roles.destroyUser');
        });
    });

    // ============================
    // ENV�O DE M�QUINAS
    // ============================
    Route::get('/enviar-maquina', [EnviarMaquinaController::class, 'create'])->name('enviar-maquina.form');
    Route::post('/enviar-maquina', [EnviarMaquinaController::class, 'store'])->name('enviar-maquina.store');

    // ============================
    // MIS WRITEUPS
    // ============================
    Route::middleware('auth')->group(function () {
        Route::get('/mis-writeups', [MisWriteupsController::class, 'index'])->name('mis-writeups.index');
        Route::post('/mis-writeups/{id}/solicitar-cambio', [MisWriteupsController::class, 'solicitarCambio'])->name('mis-writeups.solicitar-cambio');
    });

    // ============================
    // �REA DE ADMINISTRACI�N
    // ============================
    Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {

        // Dashboard principal de admin
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

        // M�quinas recibidas
        Route::get('/admin/maquinas-recibidas', [MaquinaRecibidaController::class, 'index'])
            ->name('admin.maquinas.recibidas');

        // \u279c Prefill desde EnvioMaquina hacia el formulario de creaci�n
        Route::post('/admin/maquinas-recibidas/{id}/prefill', [MaquinaRecibidaController::class, 'prefill'])
            ->name('admin.maquinas.recibidas.prefill');

        // Writeups temporales (pendientes de revisi�n)
        Route::get('/admin/writeups-temporal', [WriteupTemporalAdminController::class, 'index'])
            ->name('admin.writeups-temporal.index');
        Route::post('/admin/writeups-temporal/{id}/approve', [WriteupTemporalAdminController::class, 'approve'])
            ->name('admin.writeups-temporal.approve');
        Route::delete('/admin/writeups-temporal/{id}', [WriteupTemporalAdminController::class, 'destroy'])
            ->name('admin.writeups-temporal.destroy');

        // Writeups aprobados (listado general)
        Route::get('/admin/writeups', [WriteupAdminController::class, 'index'])
            ->name('admin.writeups.index');
        Route::delete('/admin/writeups/{id}', [WriteupAdminController::class, 'destroy'])
            ->name('admin.writeups.destroy');
    });

    // ============================
    // WRITEUPS TEMPORALES (desde home)
    // ============================
    Route::post('/writeups-temporal', [WriteupTemporalController::class, 'store'])
        ->name('writeups-temporal.store');
});

/**
 * Rutas adicionales SIN prefijo de nombre para coincidir con vistas/controladores
 * que usan nombres "admin.*" (no "dockerlabs.admin.*").
 * Se aplican los mismos middlewares de admin.
 */
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    // \u2014\u2014\u2014 M�quinas (coinciden con admin.blade.php) \u2014\u2014\u2014
    Route::post('/admin/maquinas', [MaquinaController::class, 'store'])
        ->name('admin.maquinas.store');

    Route::delete('/admin/maquinas/{maquina}', [MaquinaController::class, 'destroy'])
        ->name('admin.maquinas.destroy');

    // \u2014\u2014\u2014 Bunker Tokens (usados por el modal JS en admin.blade.php) \u2014\u2014\u2014
    // Lista (JSON)
    Route::get('/admin/bunkerlabs/tokens', [TokenAdminController::class, 'index'])
        ->name('admin.bunker.tokens.index');

    // Crear (devuelve JSON con 'plain' para mostrar el token una vez)
    Route::post('/admin/bunkerlabs/tokens', [TokenAdminController::class, 'store'])
        ->name('admin.bunker.tokens.store');

    // Activar/Desactivar (POST)
    Route::post('/admin/bunkerlabs/tokens/{id}/toggle', [TokenAdminController::class, 'toggle']);

    // Eliminar (DELETE)
    Route::delete('/admin/bunkerlabs/tokens/{id}', [TokenAdminController::class, 'destroy']);
});
