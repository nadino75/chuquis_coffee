<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        $tipoReporte = $request->get('tipo_reporte', 'dashboard');

        \Log::info("Generando reporte", [
            'tipo' => $tipoReporte,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ]);

        // Generar reporte en tiempo real
        $datosReporte = $this->generarReporteTiempoReal($tipoReporte, $fechaInicio, $fechaFin);

        // Debug: mostrar datos en log
        \Log::info("Datos del reporte generados", ['datos' => $datosReporte]);

        return view('reportes.index', compact('datosReporte', 'fechaInicio', 'fechaFin', 'tipoReporte'));
    }

    private function generarReporteTiempoReal($tipo, $fechaInicio, $fechaFin)
    {
        switch ($tipo) {
            case 'dashboard':
                return $this->reporteDashboard($fechaInicio, $fechaFin);
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
            default:
                return $this->reporteDashboard($fechaInicio, $fechaFin);
        }
    }

    private function reporteDashboard($fechaInicio, $fechaFin)
    {
        try {
            \Log::info("Generando dashboard", ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]);

            // Verificar si hay datos en el rango de fechas
            $ventasRango = Venta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin])->get();
            \Log::info("Ventas en rango: " . $ventasRango->count());

            // Estadísticas generales
            $totalVentas = Venta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin])->count();
            $totalIngresos = Venta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin])->sum('total');
            $totalPagos = Pago::whereBetween('fecha', [$fechaInicio, $fechaFin])->count();
            $totalClientes = Cliente::count();
            $totalProductos = Producto::count();
            
            \Log::info("Estadísticas calculadas", [
                'ventas' => $totalVentas,
                'ingresos' => $totalIngresos,
                'pagos' => $totalPagos
            ]);

            // Ventas por día (últimos 7 días)
            $fechaInicioSemana = now()->subDays(7)->format('Y-m-d');
            $fechaFinSemana = now()->format('Y-m-d');
            
            $ventasUltimaSemana = Venta::whereBetween('fecha_venta', [$fechaInicioSemana, $fechaFinSemana])
                ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(total) as total')
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            \Log::info("Ventas última semana", ['data' => $ventasUltimaSemana->toArray()]);

            // Productos más vendidos
            $productosMasVendidos = $this->obtenerProductosMasVendidos($fechaInicio, $fechaFin);

            // Alertas de stock
            $alertasStock = $this->obtenerAlertasStock();

            // Métodos de pago más utilizados
            $metodosPago = $this->obtenerMetodosPago($fechaInicio, $fechaFin);

            $datos = [
                'dashboard' => true,
                'estadisticas_generales' => [
                    'total_ventas' => $totalVentas,
                    'total_ingresos' => $totalIngresos,
                    'total_pagos' => $totalPagos,
                    'total_clientes' => $totalClientes,
                    'total_productos' => $totalProductos,
                ],
                'ventas_ultima_semana' => $ventasUltimaSemana,
                'productos_mas_vendidos' => $productosMasVendidos,
                'alertas_stock' => $alertasStock,
                'metodos_pago' => $metodosPago,
            ];

            \Log::info("Dashboard generado", $datos);

            return $datos;

        } catch (\Exception $e) {
            \Log::error('Error en reporte dashboard: ' . $e->getMessage());
            return [
                'dashboard' => true,
                'estadisticas_generales' => [
                    'total_ventas' => 0,
                    'total_ingresos' => 0,
                    'total_pagos' => 0,
                    'total_clientes' => 0,
                    'total_productos' => 0,
                ],
                'ventas_ultima_semana' => collect(),
                'productos_mas_vendidos' => collect(),
                'alertas_stock' => collect(),
                'metodos_pago' => collect(),
                'error' => $e->getMessage()
            ];
        }
    }

    private function reporteVentas($fechaInicio, $fechaFin)
    {
        try {
            \Log::info("Generando reporte ventas", ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]);

            // Ventas por día
            $ventasPorDia = Venta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
                ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(total) as total')
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            \Log::info("Ventas por día", ['data' => $ventasPorDia->toArray()]);

            // Productos más vendidos
            $productosMasVendidos = $this->obtenerProductosMasVendidos($fechaInicio, $fechaFin);

            return [
                'ventas_por_dia' => $ventasPorDia,
                'productos_mas_vendidos' => $productosMasVendidos,
                'total_ventas' => Venta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin])->count(),
                'total_ingresos' => Venta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin])->sum('total'),
            ];

        } catch (\Exception $e) {
            \Log::error('Error en reporte ventas: ' . $e->getMessage());
            return [
                'ventas_por_dia' => collect(),
                'productos_mas_vendidos' => collect(),
                'total_ventas' => 0,
                'total_ingresos' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    private function reportePagos($fechaInicio, $fechaFin)
    {
        try {
            \Log::info("Generando reporte pagos", ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]);

            // Pagos por día
            $pagosPorDia = Pago::whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->selectRaw('DATE(fecha) as fecha, COUNT(*) as cantidad, SUM(total_pagado) as monto_total')
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            // Métodos de pago
            $metodosPago = $this->obtenerMetodosPago($fechaInicio, $fechaFin);

            return [
                'pagos_por_dia' => $pagosPorDia,
                'metodos_pago' => $metodosPago,
                'total_pagos' => Pago::whereBetween('fecha', [$fechaInicio, $fechaFin])->count(),
                'monto_total' => Pago::whereBetween('fecha', [$fechaInicio, $fechaFin])->sum('total_pagado'),
            ];

        } catch (\Exception $e) {
            \Log::error('Error en reporte pagos: ' . $e->getMessage());
            return [
                'pagos_por_dia' => collect(),
                'metodos_pago' => collect(),
                'total_pagos' => 0,
                'monto_total' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    private function reporteProductos($fechaInicio, $fechaFin)
    {
        try {
            // Productos más vendidos
            $productosMasVendidos = $this->obtenerProductosMasVendidos($fechaInicio, $fechaFin);

            // Productos por categoría
            $productosPorCategoria = DB::table('productos')
                ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                ->selectRaw('categorias.nombre as categoria, COUNT(*) as cantidad')
                ->groupBy('categorias.id', 'categorias.nombre')
                ->get();

            // Alertas de stock
            $alertasStock = $this->obtenerAlertasStock();

            return [
                'productos_mas_vendidos' => $productosMasVendidos,
                'productos_por_categoria' => $productosPorCategoria,
                'alertas_stock' => $alertasStock,
                'total_productos' => Producto::count(),
                'valor_inventario' => Producto::sum(DB::raw('precio * stock')),
            ];

        } catch (\Exception $e) {
            \Log::error('Error en reporte productos: ' . $e->getMessage());
            return [
                'productos_mas_vendidos' => collect(),
                'productos_por_categoria' => collect(),
                'alertas_stock' => collect(),
                'total_productos' => 0,
                'valor_inventario' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    private function reporteInventario()
    {
        try {
            $alertasStock = $this->obtenerAlertasStock();

            $productosPorCategoria = DB::table('productos')
                ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                ->selectRaw('categorias.nombre as categoria, COUNT(*) as cantidad, SUM(productos.precio * productos.stock) as valor_total')
                ->groupBy('categorias.id', 'categorias.nombre')
                ->get();

            $productosSinStock = Producto::where('stock', 0)->get();
            $productosStockBajo = Producto::where('stock', '<', 10)->where('stock', '>', 0)->get();

            return [
                'alertas_stock' => $alertasStock,
                'productos_por_categoria' => $productosPorCategoria,
                'productos_sin_stock' => $productosSinStock,
                'productos_stock_bajo' => $productosStockBajo,
                'total_productos' => Producto::count(),
                'valor_total_inventario' => Producto::sum(DB::raw('precio * stock')),
            ];

        } catch (\Exception $e) {
            \Log::error('Error en reporte inventario: ' . $e->getMessage());
            return [
                'alertas_stock' => collect(),
                'productos_por_categoria' => collect(),
                'productos_sin_stock' => collect(),
                'productos_stock_bajo' => collect(),
                'total_productos' => 0,
                'valor_total_inventario' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    private function reporteClientes($fechaInicio, $fechaFin)
    {
        try {
            // Mejores clientes
            $mejoresClientes = Cliente::withCount(['ventas' => function($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
            }])
            ->withSum(['ventas' => function($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
            }], 'total')
            ->orderBy('ventas_count', 'desc')
            ->limit(10)
            ->get();

            // Clientes por ciudad
            $clientesPorCiudad = Cliente::selectRaw('ciudad, COUNT(*) as cantidad')
                ->groupBy('ciudad')
                ->orderBy('cantidad', 'desc')
                ->get();

            // Clientes nuevos
            $clientesNuevos = Cliente::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])->count();

            return [
                'mejores_clientes' => $mejoresClientes,
                'clientes_por_ciudad' => $clientesPorCiudad,
                'clientes_nuevos' => $clientesNuevos,
                'total_clientes' => Cliente::count(),
                'clientes_activos' => Cliente::has('ventas')->count(),
            ];

        } catch (\Exception $e) {
            \Log::error('Error en reporte clientes: ' . $e->getMessage());
            return [
                'mejores_clientes' => collect(),
                'clientes_por_ciudad' => collect(),
                'clientes_nuevos' => 0,
                'total_clientes' => 0,
                'clientes_activos' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    // Métodos auxiliares
    private function obtenerProductosMasVendidos($fechaInicio, $fechaFin)
    {
        try {
            $resultado = Venta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
                ->join('productos', 'ventas.producto_id', '=', 'productos.id')
                ->selectRaw('productos.id, productos.nombre, SUM(ventas.cantidad) as cantidad_vendida, SUM(ventas.total) as total_ingresos')
                ->groupBy('productos.id', 'productos.nombre')
                ->orderBy('cantidad_vendida', 'desc')
                ->limit(10)
                ->get();

            \Log::info("Productos más vendidos", ['data' => $resultado->toArray()]);
            return $resultado;

        } catch (\Exception $e) {
            \Log::error('Error obteniendo productos más vendidos: ' . $e->getMessage());
            return collect();
        }
    }

    private function obtenerAlertasStock()
    {
        $alertas = Producto::where('stock', '<', 10)
            ->select('id', 'nombre', 'stock', 'stock_minimo')
            ->orderBy('stock', 'asc')
            ->get();

        \Log::info("Alertas de stock", ['data' => $alertas->toArray()]);
        return $alertas;
    }

    private function obtenerMetodosPago($fechaInicio, $fechaFin)
    {
        $metodos = Pago::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->selectRaw('tipo_pago as metodo_pago, COUNT(*) as cantidad, SUM(total_pagado) as monto_total')
            ->groupBy('tipo_pago')
            ->orderBy('cantidad', 'desc')
            ->get();

        \Log::info("Métodos de pago", ['data' => $metodos->toArray()]);
        return $metodos;
    }

    public function descargarPDF(Request $request)
    {
        $tipoReporte = $request->get('tipo_reporte', 'dashboard');
        $fechaInicio = $request->get('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->generarReporteTiempoReal($tipoReporte, $fechaInicio, $fechaFin);

        $pdf = PDF::loadView('reportes.pdf.plantilla', [
            'datos' => $datos,
            'tipoReporte' => $tipoReporte,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'fechaGeneracion' => now()->format('d/m/Y H:i')
        ]);

        $nombreArchivo = "reporte_{$tipoReporte}_{$fechaInicio}_a_{$fechaFin}.pdf";

        return $pdf->download($nombreArchivo);
    }

    public function obtenerDatosReporte(Request $request)
    {
        $tipoReporte = $request->get('tipo_reporte', 'dashboard');
        $fechaInicio = $request->get('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->generarReporteTiempoReal($tipoReporte, $fechaInicio, $fechaFin);

        return response()->json($datos);
    }
}