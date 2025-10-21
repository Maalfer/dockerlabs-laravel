<?php

use Illuminate\Support\Facades\Route;
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
