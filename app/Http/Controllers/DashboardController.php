<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Estadísticas principales
            $estadisticas = $this->obtenerEstadisticasPrincipales();
            
            // Datos para gráficos
            $datosGraficos = $this->obtenerDatosGraficos();
            
            // Alertas del sistema
            $alertas = $this->obtenerAlertasSistema();
            
            // Ventas recientes
            $ventasRecientes = $this->obtenerVentasRecientes();

            return view('dashboard', compact(
                'estadisticas', 
                'datosGraficos', 
                'alertas', 
                'ventasRecientes'
            ));

        } catch (\Exception $e) {
            \Log::error('Error en dashboard: ' . $e->getMessage());
            
            return view('dashboard', [
                'estadisticas' => $this->estadisticasPorDefecto(),
                'datosGraficos' => $this->datosGraficosPorDefecto(),
                'alertas' => [],
                'ventasRecientes' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    private function obtenerEstadisticasPrincipales()
    {
        $hoy = now()->format('Y-m-d');
        $inicioMes = now()->startOfMonth()->format('Y-m-d');
        $inicioSemana = now()->startOfWeek()->format('Y-m-d');
        
        return [
            'ventas_hoy' => [
                'total' => Venta::whereDate('fecha_venta', $hoy)->count(),
                'ingresos' => Venta::whereDate('fecha_venta', $hoy)->sum('total'),
                'icon' => 'fas fa-shopping-cart',
                'color' => 'info',
                'titulo' => 'Ventas Hoy'
            ],
            'ventas_mes' => [
                'total' => Venta::whereBetween('fecha_venta', [$inicioMes, $hoy])->count(),
                'ingresos' => Venta::whereBetween('fecha_venta', [$inicioMes, $hoy])->sum('total'),
                'icon' => 'fas fa-chart-line',
                'color' => 'success',
                'titulo' => 'Ventas Mes'
            ],
            'total_clientes' => [
                'total' => Cliente::count(),
                'icon' => 'fas fa-users',
                'color' => 'warning',
                'titulo' => 'Total Clientes'
            ],
            'total_productos' => [
                'total' => Producto::count(),
                'icon' => 'fas fa-boxes',
                'color' => 'primary',
                'titulo' => 'Total Productos'
            ],
            'stock_bajo' => [
                'total' => Producto::where('stock', '<', 10)->count(),
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'danger',
                'titulo' => 'Stock Bajo'
            ],
            'ingresos_totales' => [
                'total' => Venta::sum('total'),
                'icon' => 'fas fa-money-bill-wave',
                'color' => 'success',
                'titulo' => 'Ingresos Totales'
            ]
        ];
    }

    private function obtenerDatosGraficos()
    {
        // Ventas de los últimos 7 días
        $ventas7Dias = Venta::whereBetween('fecha_venta', [now()->subDays(7), now()])
            ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(total) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Productos más vendidos (últimos 30 días)
        $productosMasVendidos = Venta::whereBetween('fecha_venta', [now()->subDays(30), now()])
            ->join('productos', 'ventas.producto_id', '=', 'productos.id')
            ->selectRaw('productos.nombre, SUM(ventas.cantidad) as cantidad_vendida')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('cantidad_vendida', 'desc')
            ->limit(8)
            ->get();

        // Métodos de pago más utilizados
        $metodosPago = Pago::whereBetween('fecha', [now()->subDays(30), now()])
            ->selectRaw('tipo_pago, COUNT(*) as cantidad, SUM(total_pagado) as monto_total')
            ->groupBy('tipo_pago')
            ->orderBy('cantidad', 'desc')
            ->get();

        // Ventas por categoría
        $ventasPorCategoria = Venta::whereBetween('fecha_venta', [now()->subDays(30), now()])
            ->join('productos', 'ventas.producto_id', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->selectRaw('categorias.nombre as categoria, COUNT(*) as cantidad, SUM(ventas.total) as total')
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderBy('total', 'desc')
            ->get();

        return [
            'ventas_7_dias' => $ventas7Dias,
            'productos_mas_vendidos' => $productosMasVendidos,
            'metodos_pago' => $metodosPago,
            'ventas_por_categoria' => $ventasPorCategoria
        ];
    }

    private function obtenerAlertasSistema()
    {
        $alertas = [];

        // Alertas de stock bajo
        $productosStockBajo = Producto::where('stock', '<', 10)
            ->select('nombre', 'stock', 'stock_minimo')
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        foreach ($productosStockBajo as $producto) {
            $alertas[] = [
                'tipo' => $producto->stock == 0 ? 'danger' : 'warning',
                'icon' => $producto->stock == 0 ? 'fas fa-times-circle' : 'fas fa-exclamation-triangle',
                'mensaje' => $producto->stock == 0 
                    ? "{$producto->nombre} - SIN STOCK" 
                    : "{$producto->nombre} - Stock bajo: {$producto->stock} unidades",
                'fecha' => now()->format('d/m H:i')
            ];
        }

        return $alertas;
    }

    private function obtenerVentasRecientes()
    {
        return Venta::with(['producto', 'cliente'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    private function estadisticasPorDefecto()
    {
        return [
            'ventas_hoy' => ['total' => 0, 'ingresos' => 0, 'icon' => 'fas fa-shopping-cart', 'color' => 'info', 'titulo' => 'Ventas Hoy'],
            'ventas_mes' => ['total' => 0, 'ingresos' => 0, 'icon' => 'fas fa-chart-line', 'color' => 'success', 'titulo' => 'Ventas Mes'],
            'total_clientes' => ['total' => 0, 'icon' => 'fas fa-users', 'color' => 'warning', 'titulo' => 'Total Clientes'],
            'total_productos' => ['total' => 0, 'icon' => 'fas fa-boxes', 'color' => 'primary', 'titulo' => 'Total Productos'],
            'stock_bajo' => ['total' => 0, 'icon' => 'fas fa-exclamation-triangle', 'color' => 'danger', 'titulo' => 'Stock Bajo'],
            'ingresos_totales' => ['total' => 0, 'icon' => 'fas fa-money-bill-wave', 'color' => 'success', 'titulo' => 'Ingresos Totales']
        ];
    }

    private function datosGraficosPorDefecto()
    {
        return [
            'ventas_7_dias' => collect(),
            'productos_mas_vendidos' => collect(),
            'metodos_pago' => collect(),
            'ventas_por_categoria' => collect()
        ];
    }

    /**
     * API para obtener datos del dashboard
     */
    public function obtenerDatosDashboard()
    {
        try {
            $estadisticas = $this->obtenerEstadisticasPrincipales();
            $datosGraficos = $this->obtenerDatosGraficos();
            $alertas = $this->obtenerAlertasSistema();

            return response()->json([
                'success' => true,
                'estadisticas' => $estadisticas,
                'datosGraficos' => $datosGraficos,
                'alertas' => $alertas
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en API dashboard: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}