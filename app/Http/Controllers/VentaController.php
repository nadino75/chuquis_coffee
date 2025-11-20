<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Pago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;


class VentaController extends Controller  
{
    function __construct()
    {
        $this->middleware('permission:ver-venta|crear-venta|editar-venta|borrar-venta', ['only' => ['index']]);
        $this->middleware('permission:crear-venta', ['only' => ['create','store']]);
        $this->middleware('permission:editar-venta', ['only' => ['edit','update']]);
        $this->middleware('permission:borrar-venta', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $ventas = Venta::with(['producto', 'cliente', 'pago'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('venta.index', compact('ventas'))
            ->with('i', ($request->input('page', 1) - 1) * $ventas->perPage());
    }

    public function create(): View
    {
        $productos = Producto::where('stock', '>', 0)->get();
        $clientes = Cliente::all();
        $permission = Permission::get();
        return view('venta.create', compact('productos', 'clientes', 'permission'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            Log::info('Iniciando proceso de venta', ['request_data' => $request->all()]);
            
            DB::transaction(function () use ($request) {
                // Validación para múltiples productos
                $request->validate([
                    'cliente_ci' => 'required|exists:clientes,ci',
                    'productos' => 'required|array|min:1',
                    'productos.*.id' => 'required|exists:productos,id',
                    'productos.*.cantidad' => 'required|integer|min:1',
                    'productos.*.precio' => 'required|numeric|min:0',
                    'tipo_pago' => 'required|array|min:1',
                    'tipo_pago.*' => 'required|in:efectivo,tarjeta,transferencia,qr',
                    'monto_pago' => 'required|array|min:1',
                    'monto_pago.*' => 'required|numeric|min:0',
                ]);

                Log::info('Validación pasada correctamente');

                // Verificar stock disponible para todos los productos
                foreach ($request->productos as $index => $productoCarrito) {
                    $producto = Producto::findOrFail($productoCarrito['id']);
                    Log::info("Verificando stock producto {$producto->nombre}", [
                        'solicitado' => $productoCarrito['cantidad'],
                        'disponible' => $producto->stock
                    ]);
                    
                    if ($producto->stock < $productoCarrito['cantidad']) {
                        throw new \Exception(
                            "Stock insuficiente para: {$producto->nombre}. " .
                            "Solicitado: {$productoCarrito['cantidad']}, " .
                            "Disponible: {$producto->stock}"
                        );
                    }
                }

                // Calcular total de la venta
                $totalVenta = 0;
                foreach ($request->productos as $productoCarrito) {
                    $totalVenta += $productoCarrito['precio'] * $productoCarrito['cantidad'];
                }

                $totalPagado = array_sum($request->monto_pago);

                Log::info('Cálculos de totales', [
                    'total_venta' => $totalVenta,
                    'total_pagado' => $totalPagado,
                    'diferencia' => abs($totalPagado - $totalVenta)
                ]);

                // Validar que el pago cubra el total (con margen de 0.01 por decimales)
                if (abs($totalPagado - $totalVenta) > 0.01) {
                    throw new \Exception(
                        "El total pagado ($" . number_format($totalPagado, 2) . ") " .
                        "no coincide con el total de la venta ($" . number_format($totalVenta, 2) . ")"
                    );
                }

                // Determinar tipo de pago principal
                $tipoPagoPrincipal = count($request->tipo_pago) > 1 ? 'mixto' : $request->tipo_pago[0];

                // Crear pago principal con recibo más corto (máximo 20 caracteres)
                $recibo = 'RC-' . date('His') . rand(100, 999); // Ejemplo: RC-052312345
                Log::info('Creando pago principal', ['recibo' => $recibo]);
                
                $pago = Pago::create([
                    'recibo' => $recibo,
                    'fecha' => now(),
                    'tipo_pago' => $tipoPagoPrincipal,
                    'total_pagado' => $totalPagado,
                    'cliente_ci' => $request->cliente_ci,
                ]);

                Log::info('Pago creado', ['pago_id' => $pago->id]);

                // Crear ventas y reducir stock
                foreach ($request->productos as $productoCarrito) {
                    $producto = Producto::findOrFail($productoCarrito['id']);
                    
                    Log::info('Creando venta para producto', [
                        'producto_id' => $productoCarrito['id'],
                        'cantidad' => $productoCarrito['cantidad'],
                        'precio' => $productoCarrito['precio']
                    ]);
                    
                    // Crear registro de venta
                    $venta = Venta::create([
                        'producto_id' => $productoCarrito['id'],
                        'cliente_ci' => $request->cliente_ci,
                        'pago_id' => $pago->id,
                        'precio' => $productoCarrito['precio'],
                        'cantidad' => $productoCarrito['cantidad'],
                    ]);

                    Log::info('Venta creada', ['venta_id' => $venta->id]);

                    // Reducir stock del producto
                    $stockAnterior = $producto->stock;
                    $producto->stock -= $productoCarrito['cantidad'];
                    $producto->save();
                    
                    Log::info('Stock actualizado', [
                        'producto' => $producto->nombre,
                        'stock_anterior' => $stockAnterior,
                        'stock_actual' => $producto->stock
                    ]);
                }

                Log::info('Proceso de venta completado exitosamente');
            });

            return Redirect::route('ventas.index')
                ->with('success', 'Venta registrada exitosamente y stock actualizado.');

        } catch (\Exception $e) {
            Log::error('Error al procesar venta: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return Redirect::back()
                ->withInput()
                ->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }

    public function show($id): View
    {
        $ventaPermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        $venta = Venta::with(['producto', 'cliente', 'pago'])->findOrFail($id);
        return view('venta.show', compact('venta', 'ventaPermissions'));
    }

    public function destroy($id): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id) {
                $venta = Venta::with('producto')->findOrFail($id);
                
                // Aumentar stock del producto antes de eliminar
                if ($venta->producto) {
                    $venta->producto->stock += $venta->cantidad;
                    $venta->producto->save();
                }
                
                // Eliminar pago si existe
                if ($venta->pago) {
                    $venta->pago()->delete();
                }
                
                // Eliminar venta
                $venta->delete();
            });

            return Redirect::route('ventas.index')
                ->with('success', 'Venta eliminada exitosamente y stock restaurado.');

        } catch (\Exception $e) {
            return Redirect::route('ventas.index')
                ->with('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }
}