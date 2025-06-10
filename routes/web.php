<?php

use App\Http\Controllers\ChatGptController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CorreoEntranteController;
use App\Models\CorreoEntrante;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/chat', [ChatGptController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatGptController::class, 'enviar'])->name('chat.enviar');

    // Empresa
    Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
    Route::post('/empresa', [EmpresaController::class, 'store'])->name('empresa.store');
    Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');

    // Clientes
    Route::resource('clientes', ClienteController::class);

    Route::resource('correos', CorreoEntranteController::class)->only(['index', 'show', 'destroy']);


    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// routes/web.php
Route::get('/notificaciones/correos', [CorreoEntranteController::class, 'nuevos'])->name('notificaciones.correos');
Route::post('/notificaciones/correos/{id}/notificar', function ($id) {
    CorreoEntrante::where('id', $id)->update(['notificado' => true]);
    return response()->json(['ok' => true]);
});

require __DIR__.'/auth.php';
