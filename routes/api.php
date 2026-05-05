<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\TipoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedoreController;
use App\Http\Controllers\ProveedoresProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PagosMixtoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes - JSON responses for Vue SPA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // ═══════════════════════════════════════════════════════════════════
    // ADMIN DASHBOARD
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-usuario')->get('/api/admin/dashboard', [AdminDashboardController::class, 'obtenerDatosAdmin']);

    // ═══════════════════════════════════════════════════════════════════
    // CLIENTES
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-cliente')->group(function () {
        Route::get('/api/clientes', [ClienteController::class, 'indexApi']);
        Route::get('/api/clientes/{ci}', [ClienteController::class, 'showApi']);
    });
    Route::middleware('permission:crear-cliente')->group(function () {
        Route::post('/api/clientes', [ClienteController::class, 'storeApi']);
    });
    Route::middleware('permission:editar-cliente')->group(function () {
        Route::put('/api/clientes/{ci}', [ClienteController::class, 'updateApi']);
    });
    Route::middleware('permission:borrar-cliente')->group(function () {
        Route::delete('/api/clientes/{ci}', [ClienteController::class, 'destroyApi']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // CATEGORIAS
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-categoria')->group(function () {
        Route::get('/api/categorias', [CategoriaController::class, 'indexApi']);
        Route::get('/api/categorias/{id}', [CategoriaController::class, 'showApi']);
    });
    Route::middleware('permission:crear-categoria')->group(function () {
        Route::post('/api/categorias', [CategoriaController::class, 'storeApi']);
    });
    Route::middleware('permission:editar-categoria')->group(function () {
        Route::put('/api/categorias/{id}', [CategoriaController::class, 'updateApi']);
    });
    Route::middleware('permission:borrar-categoria')->group(function () {
        Route::delete('/api/categorias/{id}', [CategoriaController::class, 'destroyApi']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // MARCAS
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-marca')->group(function () {
        Route::get('/api/marcas', [MarcaController::class, 'indexApi']);
        Route::get('/api/marcas/{id}', [MarcaController::class, 'showApi']);
    });
    Route::middleware('permission:crear-marca')->group(function () {
        Route::post('/api/marcas', [MarcaController::class, 'storeApi']);
    });
    Route::middleware('permission:editar-marca')->group(function () {
        Route::put('/api/marcas/{id}', [MarcaController::class, 'updateApi']);
    });
    Route::middleware('permission:borrar-marca')->group(function () {
        Route::delete('/api/marcas/{id}', [MarcaController::class, 'destroyApi']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // TIPOS
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-tipo')->group(function () {
        Route::get('/api/tipos', [TipoController::class, 'indexApi']);
        Route::get('/api/tipos/{id}', [TipoController::class, 'showApi']);
    });
    Route::middleware('permission:crear-tipo')->group(function () {
        Route::post('/api/tipos', [TipoController::class, 'storeApi']);
    });
    Route::middleware('permission:editar-tipo')->group(function () {
        Route::put('/api/tipos/{id}', [TipoController::class, 'updateApi']);
    });
    Route::middleware('permission:borrar-tipo')->group(function () {
        Route::delete('/api/tipos/{id}', [TipoController::class, 'destroyApi']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // PRODUCTOS
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-producto')->group(function () {
        Route::get('/api/productos', [ProductoController::class, 'indexApi']);
        Route::get('/api/productos/{id}', [ProductoController::class, 'showApi']);
    });
    Route::middleware('permission:crear-producto')->group(function () {
        Route::post('/api/productos', [ProductoController::class, 'storeApi']);
    });
    Route::middleware('permission:editar-producto')->group(function () {
        Route::put('/api/productos/{id}', [ProductoController::class, 'updateApi']);
    });
    Route::middleware('permission:borrar-producto')->group(function () {
        Route::delete('/api/productos/{id}', [ProductoController::class, 'destroyApi']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // PROVEEDORES
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-proveedor')->group(function () {
        Route::get('/api/proveedores', [ProveedoreController::class, 'indexApi']);
        Route::get('/api/proveedores/{id}', [ProveedoreController::class, 'showApi']);
    });
    Route::middleware('permission:crear-proveedor')->group(function () {
        Route::post('/api/proveedores', [ProveedoreController::class, 'storeApi']);
    });
    Route::middleware('permission:editar-proveedor')->group(function () {
        Route::put('/api/proveedores/{id}', [ProveedoreController::class, 'updateApi']);
    });
    Route::middleware('permission:borrar-proveedor')->group(function () {
        Route::delete('/api/proveedores/{id}', [ProveedoreController::class, 'destroyApi']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // PROVEEDORES-PRODUCTO
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-proveedor')->group(function () {
        Route::get('/api/proveedores-productos', [ProveedoresProductoController::class, 'indexApi']);
        Route::get('/api/proveedores-productos/{id}', [ProveedoresProductoController::class, 'showApi']);
    });
    Route::middleware('permission:crear-proveedor')->group(function () {
        Route::post('/api/proveedores-productos', [ProveedoresProductoController::class, 'storeApi']);
    });
    Route::middleware('permission:editar-proveedor')->group(function () {
        Route::put('/api/proveedores-productos/{id}', [ProveedoresProductoController::class, 'updateApi']);
    });
    Route::middleware('permission:borrar-proveedor')->group(function () {
        Route::delete('/api/proveedores-productos/{id}', [ProveedoresProductoController::class, 'destroyApi']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // VENTAS
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-venta')->group(function () {
        Route::get('/api/ventas', [VentaController::class, 'indexApi']);
        Route::get('/api/ventas/{id}', [VentaController::class, 'showApi']);
        Route::get('/api/ventas/cliente/{clienteCi}', [VentaController::class, 'ventasPorClienteApi']);
        Route::get('/api/ventas/producto/{productoId}', [VentaController::class, 'ventasPorProductoApi']);
    });
    Route::middleware('permission:crear-venta')->group(function () {
        Route::post('/api/ventas', [VentaController::class, 'storeApi']);
    });
    Route::middleware('permission:editar-venta')->group(function () {
        Route::put('/api/ventas/{id}', [VentaController::class, 'updateApi']);
    });
    Route::middleware('permission:borrar-venta')->group(function () {
        Route::delete('/api/ventas/{id}', [VentaController::class, 'destroyApi']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // PAGOS
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-pago')->group(function () {
        Route::get('/api/pagos', [PagoController::class, 'indexApi']);
        Route::get('/api/pagos/{id}', [PagoController::class, 'showApi']);
    });
    Route::middleware('permission:crear-pago')->group(function () {
        Route::post('/api/pagos', [PagoController::class, 'storeApi']);
    });
    Route::middleware('permission:editar-pago')->group(function () {
        Route::put('/api/pagos/{id}', [PagoController::class, 'updateApi']);
    });
    Route::middleware('permission:borrar-pago')->group(function () {
        Route::delete('/api/pagos/{id}', [PagoController::class, 'destroyApi']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // PAGOS MIXTO
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-pago')->group(function () {
        Route::get('/api/pagos-mixto', [PagosMixtoController::class, 'indexApi']);
        Route::get('/api/pagos-mixto/{id}', [PagosMixtoController::class, 'showApi']);
    });
    Route::middleware('permission:crear-pago')->group(function () {
        Route::post('/api/pagos-mixto', [PagosMixtoController::class, 'storeApi']);
    });
    Route::middleware('permission:editar-pago')->group(function () {
        Route::put('/api/pagos-mixto/{id}', [PagosMixtoController::class, 'updateApi']);
    });
    Route::middleware('permission:borrar-pago')->group(function () {
        Route::delete('/api/pagos-mixto/{id}', [PagosMixtoController::class, 'destroyApi']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // REPORTES
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-venta')->group(function () {
        Route::get('/api/reportes', [ReporteController::class, 'indexApi']);
        Route::get('/api/reportes/datos', [ReporteController::class, 'obtenerDatosReporte']);
        Route::get('/api/reportes/descargar-pdf', [ReporteController::class, 'descargarPDF']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // ROLES
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-rol')->group(function () {
        Route::get('/api/roles', [RoleController::class, 'indexApi']);
        Route::get('/api/roles/{id}', [RoleController::class, 'showApi']);
    });
    Route::middleware('permission:crear-rol')->group(function () {
        Route::post('/api/roles', [RoleController::class, 'storeApi']);
    });
    Route::middleware('permission:editar-rol')->group(function () {
        Route::put('/api/roles/{id}', [RoleController::class, 'updateApi']);
    });
    Route::middleware('permission:borrar-rol')->group(function () {
        Route::delete('/api/roles/{id}', [RoleController::class, 'destroyApi']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // USERS
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('permission:ver-usuario')->group(function () {
        Route::get('/api/users', [UserController::class, 'indexApi']);
        Route::get('/api/users/{id}', [UserController::class, 'showApi']);
    });
    Route::middleware('permission:crear-usuario')->group(function () {
        Route::post('/api/users', [UserController::class, 'storeApi']);
    });
    Route::middleware('permission:editar-usuario')->group(function () {
        Route::put('/api/users/{id}', [UserController::class, 'updateApi']);
    });
    Route::middleware('permission:borrar-usuario')->group(function () {
        Route::delete('/api/users/{id}', [UserController::class, 'destroyApi']);
    });
});
