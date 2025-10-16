<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\WriteupTemporalController;
use App\Http\Controllers\Admin\WriteupTemporalAdminController;
use App\Http\Controllers\Admin\WriteupAdminController;
use App\Http\Controllers\EnviarMaquinaController;
use App\Http\Controllers\Admin\MaquinaRecibidaController;
use App\Http\Controllers\MisWriteupsController;
use App\Http\Controllers\ProfileController;

// Home
Route::get('/', [HomeController::class, 'index']);

// Vistas de auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');

// Bloque admin protegido por middleware
Route::middleware([IsAdmin::class])->group(function () {
    Route::get('/admin', function () {
        return view('admin');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/maquinas-recibidas', [MaquinaRecibidaController::class, 'index'])
        ->name('maquinas.recibidas');
});

Route::middleware('auth')->group(function () {
    Route::get('/mis-writeups', [MisWriteupsController::class, 'index'])
        ->name('mis-writeups.index');

    // Nueva: el user solicita cambio de enlace de un writeup ya aprobado
    Route::post('/mis-writeups/{writeup}/solicitar-cambio', [MisWriteupsController::class, 'solicitarCambio'])
        ->name('mis-writeups.solicitar-cambio');
});


Route::get('/enviar-maquina', [EnviarMaquinaController::class, 'create'])
    ->name('enviar-maquina.form');

Route::post('/enviar-maquina', [EnviarMaquinaController::class, 'store'])
    ->name('enviar-maquina.store');


Route::get('/admin/writeups', [WriteupAdminController::class, 'index'])
    ->name('admin.writeups.index')
    ->middleware('auth');

Route::get('admin', [MaquinaController::class, 'index'])->name('admin');
Route::post('admin/maquinas', [MaquinaController::class, 'store'])->name('admin.maquinas.store');
Route::delete('admin/maquinas/{maquina}', [MaquinaController::class, 'destroy'])->name('admin.maquinas.destroy');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/registro', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/writeups-temporal', [WriteupTemporalController::class, 'store'])
    ->name('writeups-temporal.store');

Route::get('/admin/writeups-temporal', [WriteupTemporalAdminController::class, 'index'])
    ->name('admin.writeups-temporal.index')
    ->middleware('auth'); 

Route::post('/admin/writeups-temporal/{id}/approve', [WriteupTemporalAdminController::class, 'approve'])
    ->name('admin.writeups-temporal.approve')
    ->middleware('auth'); 

Route::delete('/admin/writeups-temporal/{id}', [WriteupTemporalAdminController::class, 'destroy'])
    ->name('admin.writeups-temporal.destroy')
    ->middleware('auth'); 
