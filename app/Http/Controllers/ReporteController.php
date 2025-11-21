<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;
use App\Models\Venta;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        $tipoReporte = $request->get('tipo_reporte', 'ventas');

        // Generar reporte en tiempo real
        $datosReporte = $this->generarReporteTiempoReal($tipoReporte, $fechaInicio, $fechaFin);

        return view('reportes.index', compact('datosReporte', 'fechaInicio', 'fechaFin', 'tipoReporte'));
    }

    private function generarReporteTiempoReal($tipo, $fechaInicio, $fechaFin)
    {
        switch ($tipo) {
            case 'ventas':
                return $this->reporteVentas($fechaInicio, $fechaFin);
            case 'pagos':
                return $this->reportePagos($fechaInicio, $fechaFin);
            case 'productos':
                return $this->reporteProductos($fechaInicio, $fechaFin);
            case 'inventario':
                return $this->reporteInventario();
            case 'clientes':
                return $this->reporteClientes($fechaInicio, $fechaFin);
            case 'general':
                return $this->reporteGeneral($fechaInicio, $fechaFin);
            default:
                return $this->reporteVentas($fechaInicio, $fechaFin);
        }
    }

    private function reporteVentas($fechaInicio, $fechaFin)
    {
        try {
            // Primero, verifiquemos la estructura de la tabla ventas
            $ventaEjemplo = Venta::first();
            
            // Ventas por día - consulta más segura
            $ventasPorDia = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                ->selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad_ventas')
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            // Si existe columna total, agregarla
            if ($ventaEjemplo && isset($ventaEjemplo->total)) {
                $ventasConTotal = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                    ->selectRaw('SUM(total) as monto_total')
                    ->first();
                
                $montoTotal = $ventasConTotal->monto_total ?? 0;
            } else {
                // Calcular total sumando detalles de venta si existe la relación
                $montoTotal = 0;
                if (method_exists(Venta::class, 'detalles')) {
                    $montoTotal = DB::table('venta_detalles')
                        ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                        ->whereBetween('ventas.created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                        ->sum(DB::raw('venta_detalles.cantidad * venta_detalles.precio'));
                }
            }

            // Ventas por vendedor
            $ventasPorVendedor = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                ->join('users', 'ventas.usuario_id', '=', 'users.id')
                ->selectRaw('users.name as vendedor, COUNT(*) as cantidad_ventas')
                ->groupBy('users.id', 'users.name')
                ->orderBy('cantidad_ventas', 'desc')
                ->get();

            // Productos más vendidos (si existe la tabla venta_detalles)
            $productosMasVendidos = collect();
            if (method_exists(Venta::class, 'detalles')) {
                $productosMasVendidos = DB::table('venta_detalles')
                    ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                    ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
                    ->whereBetween('ventas.created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                    ->selectRaw('productos.nombre, SUM(venta_detalles.cantidad) as cantidad_vendida')
                    ->groupBy('productos.id', 'productos.nombre')
                    ->orderBy('cantidad_vendida', 'desc')
                    ->limit(10)
                    ->get();
            }

            return [
                'ventas_totales' => Venta::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])->count(),
                'monto_total' => $montoTotal,
                'ventas_por_dia' => $ventasPorDia,
                'ventas_por_vendedor' => $ventasPorVendedor,
                'productos_mas_vendidos' => $productosMasVendidos,
                'ventas_por_estado' => Venta::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                    ->selectRaw('estado, COUNT(*) as cantidad')
                    ->groupBy('estado')
                    ->get()
            ];

        } catch (\Exception $e) {
            // En caso de error, retornar estructura básica
            return [
                'ventas_totales' => 0,
                'monto_total' => 0,
                'ventas_por_dia' => collect(),
                'ventas_por_vendedor' => collect(),
                'productos_mas_vendidos' => collect(),
                'ventas_por_estado' => collect()
            ];
        }
    }

    private function reportePagos($fechaInicio, $fechaFin)
    {
        try {
            // Verificar estructura de la tabla pagos
            $pagoEjemplo = Pago::first();
            
            $pagosPorDia = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                ->selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad_pagos')
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            // Si existe columna monto
            $montoTotal = 0;
            if ($pagoEjemplo && isset($pagoEjemplo->monto)) {
                $montoTotal = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])->sum('monto');
            }

            // Pagos por método si existe la columna
            $pagosPorMetodo = collect();
            if ($pagoEjemplo && isset($pagoEjemplo->metodo_pago)) {
                $pagosPorMetodo = Pago::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                    ->selectRaw('metodo_pago, COUNT(*) as cantidad')
                    ->groupBy('metodo_pago')
                    ->get();
            }

            return [
                'pagos_totales' => Pago::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])->count(),
                'monto_total' => $montoTotal,
                'pagos_por_dia' => $pagosPorDia,
                'pagos_por_metodo' => $pagosPorMetodo,
                'pagos_por_estado' => Pago::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                    ->selectRaw('estado, COUNT(*) as cantidad')
                    ->groupBy('estado')
                    ->get()
            ];

        } catch (\Exception $e) {
            return [
                'pagos_totales' => 0,
                'monto_total' => 0,
                'pagos_por_dia' => collect(),
                'pagos_por_metodo' => collect(),
                'pagos_por_estado' => collect()
            ];
        }
    }

    private function reporteProductos($fechaInicio, $fechaFin)
    {
        try {
            $productosVendidos = collect();
            
            if (method_exists(Venta::class, 'detalles')) {
                $productosVendidos = DB::table('venta_detalles')
                    ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                    ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
                    ->whereBetween('ventas.created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                    ->selectRaw('productos.id, productos.nombre, SUM(venta_detalles.cantidad) as cantidad_vendida')
                    ->groupBy('productos.id', 'productos.nombre')
                    ->get();
            }

            return [
                'total_productos' => Producto::count(),
                'productos_vendidos' => $productosVendidos,
                'productos_stock_bajo' => Producto::where('stock', '<', 10)->count(),
                'valor_inventario' => Producto::sum(DB::raw('precio * stock')),
                'productos_por_categoria' => DB::table('productos')
                    ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                    ->selectRaw('categorias.nombre as categoria, COUNT(*) as cantidad')
                    ->groupBy('categorias.id', 'categorias.nombre')
                    ->get()
            ];

        } catch (\Exception $e) {
            return [
                'total_productos' => 0,
                'productos_vendidos' => collect(),
                'productos_stock_bajo' => 0,
                'valor_inventario' => 0,
                'productos_por_categoria' => collect()
            ];
        }
    }

    private function reporteInventario()
    {
        try {
            return [
                'total_productos' => Producto::count(),
                'productos_stock_bajo' => Producto::where('stock', '<', 10)->get(),
                'productos_sin_stock' => Producto::where('stock', 0)->get(),
                'valor_total_inventario' => Producto::sum(DB::raw('precio * stock')),
                'productos_por_categoria' => DB::table('productos')
                    ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                    ->selectRaw('categorias.nombre as categoria, COUNT(*) as cantidad')
                    ->groupBy('categorias.id', 'categorias.nombre')
                    ->get(),
                'top_productos_valor' => Producto::select('*')
                    ->selectRaw('precio * stock as valor_total')
                    ->orderBy('valor_total', 'desc')
                    ->limit(10)
                    ->get()
            ];

        } catch (\Exception $e) {
            return [
                'total_productos' => 0,
                'productos_stock_bajo' => collect(),
                'productos_sin_stock' => collect(),
                'valor_total_inventario' => 0,
                'productos_por_categoria' => collect(),
                'top_productos_valor' => collect()
            ];
        }
    }

    private function reporteClientes($fechaInicio, $fechaFin)
    {
        try {
            $mejoresClientes = Cliente::withCount(['ventas' => function($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59']);
            }])
            ->orderBy('ventas_count', 'desc')
            ->limit(10)
            ->get();

            return [
                'total_clientes' => Cliente::count(),
                'clientes_activos' => Cliente::has('ventas')->count(),
                'mejores_clientes' => $mejoresClientes,
                'clientes_nuevos' => Cliente::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])->count(),
                'clientes_por_ciudad' => Cliente::selectRaw('ciudad, COUNT(*) as cantidad')
                    ->groupBy('ciudad')
                    ->orderBy('cantidad', 'desc')
                    ->get()
            ];

        } catch (\Exception $e) {
            return [
                'total_clientes' => 0,
                'clientes_activos' => 0,
                'mejores_clientes' => collect(),
                'clientes_nuevos' => 0,
                'clientes_por_ciudad' => collect()
            ];
        }
    }

    private function reporteGeneral($fechaInicio, $fechaFin)
    {
        return [
            'resumen_ventas' => $this->reporteVentas($fechaInicio, $fechaFin),
            'resumen_pagos' => $this->reportePagos($fechaInicio, $fechaFin),
            'resumen_clientes' => $this->reporteClientes($fechaInicio, $fechaFin),
            'resumen_inventario' => $this->reporteInventario()
        ];
    }

    public function descargarReporte(Request $request)
    {
        $tipoReporte = $request->get('tipo_reporte', 'ventas');
        $fechaInicio = $request->get('fecha_inicio', now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->generarReporteTiempoReal($tipoReporte, $fechaInicio, $fechaFin);

        $filename = "reporte_{$tipoReporte}_{$fechaInicio}_a_{$fechaFin}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\""
        ];

        $callback = function() use ($datos, $tipoReporte) {
            $file = fopen('php://output', 'w');
            
            switch ($tipoReporte) {
                case 'ventas':
                    fputcsv($file, ['Fecha', 'Cantidad de Ventas']);
                    foreach ($datos['ventas_por_dia'] as $fila) {
                        fputcsv($file, [
                            $fila->fecha,
                            $fila->cantidad_ventas
                        ]);
                    }
                    break;
                case 'pagos':
                    fputcsv($file, ['Fecha', 'Cantidad de Pagos']);
                    foreach ($datos['pagos_por_dia'] as $fila) {
                        fputcsv($file, [
                            $fila->fecha,
                            $fila->cantidad_pagos
                        ]);
                    }
                    break;
                default:
                    fputcsv($file, ['Datos', 'Valor']);
                    fputcsv($file, ['Reporte', $tipoReporte]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Métodos adicionales para API/AJAX
    public function obtenerDatosReporte(Request $request)
    {
        $tipoReporte = $request->get('tipo_reporte', 'ventas');
        $fechaInicio = $request->get('fecha_inicio', now()->subMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->generarReporteTiempoReal($tipoReporte, $fechaInicio, $fechaFin);

        return response()->json($datos);
    }

    public function crearReporteGuardado(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:ventas,pagos,productos,inventario,clientes,general',
        ]);

        $filtros = $request->only(['fecha_inicio', 'fecha_fin']);
        $datos = $this->generarReporteTiempoReal($request->tipo, $filtros['fecha_inicio'], $filtros['fecha_fin']);

        $reporte = Reporte::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'filtros' => $filtros,
            'datos' => $datos,
            'usuario_id' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reporte guardado correctamente',
            'reporte' => $reporte
        ]);
    }
}