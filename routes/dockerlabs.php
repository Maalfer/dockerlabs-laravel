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
    // AUTENTICACIÓN
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
    // PERFIL Y GESTIÓN DE ROLES
    // ============================
    Route::middleware('auth')->group(function () {
        Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/perfil', [ProfileController::class, 'update'])->name('profile.update');

        Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
            Route::get('/perfil/roles', [ProfileController::class, 'rolesIndex'])->name('profile.roles.index');
            Route::post('/perfil/roles', [ProfileController::class, 'rolesUpdate'])->name('profile.roles.update');
            Route::delete('/perfil/roles/{user}', [ProfileController::class, 'destroyUser'])->name('profile.roles.destroyUser');
        });
    });

    // ============================
    // ENVÍO DE MÁQUINAS
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
    // ÁREA DE ADMINISTRACIÓN
    // ============================
    Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {

        // Dashboard principal de admin
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

        // Máquinas recibidas
        Route::get('/admin/maquinas-recibidas', [MaquinaRecibidaController::class, 'index'])
            ->name('admin.maquinas.recibidas');

        // Writeups temporales (pendientes de revisión)
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
