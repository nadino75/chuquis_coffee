<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\VentaDetalle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

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
        $ventas = Venta::with(['cliente', 'pago', 'ventaProductos.producto'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $ventas->getCollection()->transform(function ($venta) {
            $this->hidratarCamposLegacyVenta($venta);
            return $venta;
        });

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
            Log::info('Iniciando proceso de venta', ['cliente_ci' => $request->cliente_ci]);
            
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

                $cliente = Cliente::where('ci', $request->cliente_ci)->firstOrFail();

                // Crear pago principal con recibo más corto
                $recibo = 'RC-' . date('His') . random_int(100, 999);
                Log::info('Creando pago principal', ['recibo' => $recibo]);
                
                $pago = Pago::create([
                    'recibo' => $recibo,
                    'fecha' => now(),
                    'tipo_pago' => $tipoPagoPrincipal,
                    'total_pagado' => $totalPagado,
                    'cliente_ci' => $request->cliente_ci,
                ]);

                Log::info('Pago creado', ['pago_id' => $pago->id]);

                // Crear cabecera de venta
                $venta = Venta::create([
                    'fecha_venta' => now()->toDateString(),
                    'suma_total' => $totalVenta,
                    'cliente_id' => $cliente->id,
                    'pago_id' => $pago->id,
                    'detalles' => 'Venta generada desde punto de venta',
                ]);

                // Crear detalle de venta por cada producto
                foreach ($request->productos as $productoCarrito) {
                    $producto = Producto::findOrFail($productoCarrito['id']);
                    $subtotal = $productoCarrito['precio'] * $productoCarrito['cantidad'];
                    
                    Log::info('Creando venta para producto', [
                        'producto_id' => $productoCarrito['id'],
                        'cantidad' => $productoCarrito['cantidad'],
                        'precio' => $productoCarrito['precio'],
                        'subtotal' => $subtotal
                    ]);
                    
                    VentaDetalle::create([
                        'id_producto' => $productoCarrito['id'],
                        'id_venta' => $venta->id,
                        'precio' => $productoCarrito['precio'],
                        'cantidad' => $productoCarrito['cantidad'],
                    ]);

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
        $venta = Venta::with(['cliente', 'pago', 'ventaProductos.producto'])->findOrFail($id);
        $this->hidratarCamposLegacyVenta($venta);
        
        // Obtener todas las ventas con el mismo pago_id para mostrar la venta completa
        $ventasDelMismoPago = Venta::with('ventaProductos.producto')
            ->where('pago_id', $venta->pago_id)
            ->get()
            ->map(function ($item) {
                $this->hidratarCamposLegacyVenta($item);
                return $item;
            });

        // Obtener permisos del usuario actual
        $ventaPermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id", auth()->user()->roles->first()->id ?? 0)
            ->get();

        return view('venta.show', compact('venta', 'ventasDelMismoPago', 'ventaPermissions'));
    }

    public function destroy($id): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id) {
                $venta = Venta::with(['ventaProductos.producto', 'pago'])->findOrFail($id);
                
                // Restaurar stock de cada item del detalle
                foreach ($venta->ventaProductos as $detalle) {
                    if ($detalle->producto) {
                        $detalle->producto->stock += $detalle->cantidad;
                        $detalle->producto->save();
                        Log::info("Stock restaurado para producto: {$detalle->producto->nombre}");
                    }
                }
                
                // Verificar si hay más ventas con el mismo pago_id
                $otrasVentasConMismoPago = Venta::where('pago_id', $venta->pago_id)
                    ->where('id', '!=', $venta->id)
                    ->exists();
                
                Log::info("Otras ventas con mismo pago: " . ($otrasVentasConMismoPago ? 'Sí' : 'No'));
                
                // Eliminar pago solo si no hay más ventas asociadas
                if (!$otrasVentasConMismoPago && $venta->pago) {
                    $venta->pago->delete();
                    Log::info("Pago eliminado: {$venta->pago->id}");
                }
                
                // Eliminar venta
                $venta->delete();
                Log::info("Venta eliminada: {$venta->id}");
            });

            return Redirect::route('ventas.index')
                ->with('success', 'Venta eliminada exitosamente y stock restaurado.');

        } catch (\Exception $e) {
            Log::error('Error al eliminar venta: ' . $e->getMessage());
            return Redirect::route('ventas.index')
                ->with('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Método adicional para obtener ventas por cliente
     */
    public function ventasPorCliente($clienteCi): View
    {
        $cliente = Cliente::where('ci', $clienteCi)->firstOrFail();
        $ventas = Venta::with(['pago', 'ventaProductos.producto'])
            ->where('cliente_id', $cliente->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $ventas->getCollection()->transform(function ($venta) {
            $this->hidratarCamposLegacyVenta($venta);
            return $venta;
        });

        return view('venta.index', compact('ventas'));
    }

    /**
     * Método adicional para obtener ventas por producto
     */
    public function ventasPorProducto($productoId): View
    {
        $producto = Producto::findOrFail($productoId);
        $ventas = Venta::with(['cliente', 'pago', 'ventaProductos.producto'])
            ->whereHas('ventaProductos', function ($query) use ($productoId) {
                $query->where('id_producto', $productoId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $ventas->getCollection()->transform(function ($venta) {
            $this->hidratarCamposLegacyVenta($venta);
            return $venta;
        });

        return view('venta.index', compact('ventas'));
    }

    private function hidratarCamposLegacyVenta(Venta $venta): void
    {
        $detalle = $venta->ventaProductos->first();

        if ($detalle && $detalle->producto) {
            $venta->setRelation('producto', $detalle->producto);
            $venta->setAttribute('cantidad', $detalle->cantidad);
            $venta->setAttribute('precio', $detalle->precio);
        } else {
            $venta->setAttribute('cantidad', 0);
            $venta->setAttribute('precio', 0);
        }

        $venta->setAttribute('cliente_ci', optional($venta->cliente)->ci);
        $venta->setAttribute('total', $venta->suma_total);
    }

    // API Methods
    public function indexApi(Request $request): JsonResponse
    {
        $ventas = Venta::with(['cliente', 'pago', 'ventaProductos.producto'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $ventas->getCollection()->transform(function ($venta) {
            $this->hidratarCamposLegacyVenta($venta);
            return $venta;
        });

        return response()->json($ventas);
    }

    public function showApi($id): JsonResponse
    {
        $venta = Venta::with(['cliente', 'pago', 'ventaProductos.producto'])->findOrFail($id);
        $this->hidratarCamposLegacyVenta($venta);
        return response()->json($venta);
    }

    public function storeApi(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'cliente_ci' => 'required|exists:clientes,ci',
                'producto_id' => 'required|exists:productos,id',
                'cantidad' => 'required|integer|min:1',
                'precio' => 'required|numeric|min:0',
                'tipo_pago' => 'required|in:efectivo,tarjeta,transferencia,qr,mixto',
                'pagos_mixtos' => 'required_if:tipo_pago,mixto|array|min:1',
                'pagos_mixtos.*.tipo_pago' => 'required_with:pagos_mixtos|in:efectivo,tarjeta,transferencia,qr',
                'pagos_mixtos.*.monto' => 'required_with:pagos_mixtos|numeric|min:0.01',
            ]);

            $producto = Producto::findOrFail($validated['producto_id']);
            if ($producto->stock < $validated['cantidad']) {
                return response()->json(['message' => 'Stock insuficiente'], 422);
            }

            $total = $validated['precio'] * $validated['cantidad'];

            if ($validated['tipo_pago'] === 'mixto') {
                $totalPagado = array_sum(array_column($validated['pagos_mixtos'], 'monto'));
                if (abs($totalPagado - $total) > 0.01) {
                    return response()->json([
                        'message' => 'El total de pagos mixtos no coincide con el total de la venta.'
                    ], 422);
                }
            }

            // Crear pago principal
            $pago = Pago::create([
                'recibo' => 'RC-' . date('His') . rand(100, 999),
                'fecha' => now(),
                'tipo_pago' => $validated['tipo_pago'],
                'total_pagado' => $total,
                'cliente_ci' => $validated['cliente_ci'],
            ]);

            // Si es mixto, crear sub-pagos
            if ($validated['tipo_pago'] === 'mixto') {
                foreach ($validated['pagos_mixtos'] as $subPago) {
                    Pago::create([
                        'recibo' => 'RC-MX-' . date('His') . rand(100, 999),
                        'fecha' => now(),
                        'tipo_pago' => $subPago['tipo_pago'],
                        'total_pagado' => $subPago['monto'],
                        'cliente_ci' => $validated['cliente_ci'],
                        'pago_mixto_id' => $pago->id,
                    ]);
                }
            }

            $cliente = Cliente::where('ci', $validated['cliente_ci'])->firstOrFail();

            $venta = Venta::create([
                'fecha_venta' => now()->toDateString(),
                'suma_total' => $total,
                'cliente_id' => $cliente->id,
                'pago_id' => $pago->id,
                'detalles' => 'Venta desde API',
            ]);

            VentaDetalle::create([
                'id_producto' => $validated['producto_id'],
                'id_venta' => $venta->id,
                'precio' => $validated['precio'],
                'cantidad' => $validated['cantidad'],
            ]);

            $producto->stock -= $validated['cantidad'];
            $producto->save();

            DB::commit();
            return response()->json($venta->load(['cliente', 'pago']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al procesar la venta: ' . $e->getMessage()], 500);
        }
    }

    public function updateApi(Request $request, $id): JsonResponse
    {
        return response()->json(['message' => 'Actualización de ventas no soportada via API'], 405);
    }

    public function destroyApi($id): JsonResponse
    {
        try {
            DB::transaction(function () use ($id) {
                $venta = Venta::with(['ventaProductos.producto', 'pago'])->findOrFail($id);
                foreach ($venta->ventaProductos as $detalle) {
                    if ($detalle->producto) {
                        $detalle->producto->stock += $detalle->cantidad;
                        $detalle->producto->save();
                    }
                }
                $otrasVentas = Venta::where('pago_id', $venta->pago_id)->where('id', '!=', $venta->id)->exists();
                if (!$otrasVentas && $venta->pago) {
                    $venta->pago->delete();
                }
                $venta->delete();
            });
            return response()->json(['message' => 'Venta eliminada exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar la venta'], 500);
        }
    }

    public function ventasPorClienteApi($clienteCi): JsonResponse
    {
        $cliente = Cliente::where('ci', $clienteCi)->firstOrFail();
        $ventas = Venta::with(['pago', 'ventaProductos.producto'])
            ->where('cliente_id', $cliente->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return response()->json($ventas);
    }

    public function ventasPorProductoApi($productoId): JsonResponse
    {
        $producto = Producto::findOrFail($productoId);
        $ventas = Venta::with(['cliente', 'pago', 'ventaProductos.producto'])
            ->whereHas('ventaProductos', function ($query) use ($productoId) {
                $query->where('id_producto', $productoId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return response()->json($ventas);
    }
}