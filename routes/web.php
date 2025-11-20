<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedoreController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TipoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProveedoresProductoController;
use App\Http\Controllers\ContactController; // Agregar este use si creas un controlador

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Raíz muestra la página de welcome (pública)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Ruta para el formulario de contacto (pública)
Route::post('/contact', function () {
    // Aquí puedes procesar el formulario de contacto
    return back()->with('success', 'Mensaje enviado correctamente');
})->name('contact.submit');

// Dashboard protegido
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil del usuario (solo autenticado)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de autenticación (Breeze)
require __DIR__ . '/auth.php';

// Rutas protegidas por login para CRUDs
Route::middleware(['auth'])->group(function () {
    Route::resource('proveedores', ProveedoreController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('tipos', TipoController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('ventas', VentaController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('marcas', MarcaController::class);
    Route::resource('proveedores_productos', ProveedoresProductoController::class);
    Route::resource('pagos', PagoController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});