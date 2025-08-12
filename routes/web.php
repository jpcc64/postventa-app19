<?php
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ParteController;
use App\Http\Controllers\Login;
use App\Http\Controllers\ProductoController;

// Página principal protegida
Route::get('/', [ClienteController::class, 'index'])->name('home')->middleware('auth');

// Formulario de login
Route::get('/login', function () {
    return view('login');
})->name('login');

//Procesar login
Route::post('/login', [Login::class, 'login']);

// Logout
Route::post('/logout', [Login::class, 'logout'])->name('logout');

// Clientes
Route::get('/clientes', [ClienteController::class, 'index'])->middleware('auth');

Route::get('/clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar')->middleware('auth');
Route::get('/clientes/{cif}', [ClienteController::class, 'show'])->name('clientes.show')->middleware('auth');

// Avisar a cliente por whatsapp
Route::post('/avisar/{cliente}', [ClienteController::class, 'avisar'])->name('avisar')->middleware('auth');

// Dashboard (crea o borra usuarios)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// AVISAR A CLIENTE
Route::post('/avisar/{id}', [ClienteController::class, 'avisar'])->name('avisar')->middleware('auth');


// CREAR LLAMADA DE SERVICIO
Route::get('/parte', [ParteController::class, 'index'])->name('parte')->middleware('auth');
Route::post('/parte', [ParteController::class, 'crear'])->name('parte.crear')->middleware('auth');
Route::get('/parte/buscar', [ParteController::class, 'buscar'])->name('parte.buscar')->middleware('auth');
Route::get('/parte/formulario/{callID}', [ParteController::class, 'showParte'])->name('parte.formulario')->middleware('auth');
Route::get('/parte/sugerencias', [ParteController::class, 'sugerencias'])->name('buscar.sugerencias');
Route::get('/producto/sugerencias', [ProductoController::class, 'consultarProductos'])->name('producto.sugerencias');