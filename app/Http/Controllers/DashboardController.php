<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Venta;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Cliente;

class DashboardController extends Controller
{
    public function obtenerDatosDashboard()
    {
        try {
            $roleName = Auth::user()->roles->first()?->name ?? 'Vistas';

            if ($roleName === 'Cajero') {
                return $this->datosCajero();
            }

            if ($roleName === 'Contador') {
                return $this->datosContador();
            }

            return $this->datosGenerales();

        } catch (\Exception $e) {
            Log::error('Error en API dashboard: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── DASHBOARD GENERAL (Admin / Gerente / Ventas / Vistas) ───────────────

    private function datosGenerales()
    {
        return response()->json([
            'success' => true,
            'rol' => 'general',
            'estadisticas' => $this->estadisticasGenerales(),
            'datosGraficos' => $this->graficosGenerales(),
            'alertas' => $this->alertasSistema(),
            'ventasRecientes' => $this->ventasRecientes(),
        ]);
    }

    private function estadisticasGenerales(): array
    {
        $hoy = now()->format('Y-m-d');
        $inicioMes = now()->startOfMonth()->format('Y-m-d');

        return [
            'ventas_hoy' => [
                'total' => Venta::whereDate('fecha_venta', $hoy)->count(),
                'ingresos' => Venta::whereDate('fecha_venta', $hoy)->sum('suma_total'),
                'icon' => 'fas fa-coffee', 'color' => 'info', 'titulo' => 'Ventas Hoy',
            ],
            'ventas_mes' => [
                'total' => Venta::whereBetween('fecha_venta', [$inicioMes, $hoy])->count(),
                'ingresos' => Venta::whereBetween('fecha_venta', [$inicioMes, $hoy])->sum('suma_total'),
                'icon' => 'fas fa-chart-bar', 'color' => 'success', 'titulo' => 'Ventas Mes',
            ],
            'total_clientes' => [
                'total' => Cliente::count(),
                'icon' => 'fas fa-users', 'color' => 'warning', 'titulo' => 'Clientes',
            ],
            'total_productos' => [
                'total' => Producto::count(),
                'icon' => 'fas fa-box-open', 'color' => 'primary', 'titulo' => 'Productos',
            ],
            'stock_bajo' => [
                'total' => Producto::whereColumn('stock', '<', 'stock_minimo')->count(),
                'icon' => 'fas fa-exclamation-triangle', 'color' => 'danger', 'titulo' => 'Stock Bajo',
            ],
            'ingresos_totales' => [
                'total' => Venta::sum('suma_total'),
                'icon' => 'fas fa-dollar-sign', 'color' => 'success', 'titulo' => 'Ingresos Totales',
            ],
        ];
    }

    private function graficosGenerales(): array
    {
        $ventas7Dias = Venta::whereBetween('fecha_venta', [now()->subDays(6)->format('Y-m-d'), now()->format('Y-m-d')])
            ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(suma_total) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $productosMasVendidos = DB::table('venta_productos')
            ->join('productos', 'venta_productos.id_producto', '=', 'productos.id')
            ->join('ventas', 'venta_productos.id_venta', '=', 'ventas.id')
            ->where('ventas.fecha_venta', '>=', now()->subDays(30)->format('Y-m-d'))
            ->selectRaw('productos.nombre, SUM(venta_productos.cantidad) as cantidad_vendida')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('cantidad_vendida')
            ->limit(8)
            ->get();

        $metodosPago = Pago::where('fecha', '>=', now()->subDays(30)->format('Y-m-d'))
            ->selectRaw('tipo_pago, COUNT(*) as cantidad, SUM(total_pagado) as monto_total')
            ->groupBy('tipo_pago')
            ->orderByDesc('cantidad')
            ->get();

        $ventasPorCategoria = DB::table('ventas')
            ->join('venta_productos', 'ventas.id', '=', 'venta_productos.id_venta')
            ->join('productos', 'venta_productos.id_producto', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('ventas.fecha_venta', '>=', now()->subDays(30)->format('Y-m-d'))
            ->selectRaw('categorias.nombre as categoria, SUM(venta_productos.precio * venta_productos.cantidad) as total')
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderByDesc('total')
            ->get();

        return [
            'ventas_7_dias' => $ventas7Dias,
            'productos_mas_vendidos' => $productosMasVendidos,
            'metodos_pago' => $metodosPago,
            'ventas_por_categoria' => $ventasPorCategoria,
        ];
    }

    // ─── DASHBOARD CAJERO ────────────────────────────────────────────────────

    private function datosCajero()
    {
        $hoy = now()->format('Y-m-d');

        $ventasHoy = Venta::whereDate('fecha_venta', $hoy);
        $totalVentasHoy = (clone $ventasHoy)->count();
        $montoVentasHoy = (clone $ventasHoy)->sum('suma_total');

        // Ventas por hora del día
        $ventasPorHora = Venta::whereDate('created_at', $hoy)
            ->selectRaw('HOUR(created_at) as hora, COUNT(*) as cantidad, SUM(suma_total) as total')
            ->groupBy('hora')
            ->orderBy('hora')
            ->get();

        // Métodos de pago de hoy
        $metodosPagoHoy = Pago::whereDate('fecha', $hoy)
            ->selectRaw('tipo_pago, COUNT(*) as cantidad, SUM(total_pagado) as monto')
            ->groupBy('tipo_pago')
            ->get();

        // Mis ventas recientes de hoy
        $ventasHoyDetalle = Venta::with(['cliente', 'ventaProductos.producto', 'pago'])
            ->whereDate('fecha_venta', $hoy)
            ->orderByDesc('created_at')
            ->limit(15)
            ->get()
            ->map(function ($v) {
                $detalle = $v->ventaProductos->first();
                $v->setAttribute('producto_nombre', $detalle?->producto?->nombre ?? 'Varios');
                return $v;
            });

        // Alertas de stock 0 (bloqueadores de venta)
        $sinStock = Producto::where('stock', 0)->select('nombre', 'stock')->orderBy('nombre')->limit(10)->get();

        return response()->json([
            'success' => true,
            'rol' => 'cajero',
            'estadisticas' => [
                'ventas_hoy' => [
                    'total' => $totalVentasHoy,
                    'ingresos' => $montoVentasHoy,
                    'icon' => 'fas fa-cash-register', 'color' => 'success', 'titulo' => 'Ventas Hoy',
                ],
                'productos_disponibles' => [
                    'total' => Producto::where('stock', '>', 0)->count(),
                    'icon' => 'fas fa-box-open', 'color' => 'primary', 'titulo' => 'Productos Disponibles',
                ],
                'productos_sin_stock' => [
                    'total' => Producto::where('stock', 0)->count(),
                    'icon' => 'fas fa-exclamation-triangle', 'color' => 'danger', 'titulo' => 'Sin Stock',
                ],
                'clientes_registrados' => [
                    'total' => Cliente::count(),
                    'icon' => 'fas fa-users', 'color' => 'info', 'titulo' => 'Clientes',
                ],
            ],
            'datosGraficos' => [
                'ventas_por_hora' => $ventasPorHora,
                'metodos_pago_hoy' => $metodosPagoHoy,
            ],
            'ventasHoy' => $ventasHoyDetalle,
            'sinStock' => $sinStock,
        ]);
    }

    // ─── DASHBOARD CONTADOR ──────────────────────────────────────────────────

    private function datosContador()
    {
        $inicioMesActual = now()->startOfMonth()->format('Y-m-d');
        $finMesActual = now()->format('Y-m-d');
        $inicioMesAnterior = now()->subMonth()->startOfMonth()->format('Y-m-d');
        $finMesAnterior = now()->subMonth()->endOfMonth()->format('Y-m-d');

        $ingresosMesActual = Venta::whereBetween('fecha_venta', [$inicioMesActual, $finMesActual])->sum('suma_total');
        $ingresosMesAnterior = Venta::whereBetween('fecha_venta', [$inicioMesAnterior, $finMesAnterior])->sum('suma_total');
        $variacion = $ingresosMesAnterior > 0
            ? round((($ingresosMesActual - $ingresosMesAnterior) / $ingresosMesAnterior) * 100, 1)
            : 0;

        $transaccionesMes = Venta::whereBetween('fecha_venta', [$inicioMesActual, $finMesActual])->count();
        $ticketPromedio = $transaccionesMes > 0 ? round($ingresosMesActual / $transaccionesMes, 2) : 0;

        // Ingresos por mes (últimos 6 meses)
        $ingresosPorMes = collect();
        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            $inicio = $mes->copy()->startOfMonth()->format('Y-m-d');
            $fin = $mes->copy()->endOfMonth()->format('Y-m-d');
            $total = Venta::whereBetween('fecha_venta', [$inicio, $fin])->sum('suma_total');
            $ingresosPorMes->push([
                'mes' => $mes->locale('es')->isoFormat('MMM YYYY'),
                'total' => round($total, 2),
            ]);
        }

        // Distribución de ingresos por método de pago (en $)
        $ingresosPorMetodo = Pago::where('fecha', '>=', now()->subDays(30)->format('Y-m-d'))
            ->selectRaw('tipo_pago, COUNT(*) as cantidad, SUM(total_pagado) as monto_total')
            ->groupBy('tipo_pago')
            ->orderByDesc('monto_total')
            ->get();

        // Ingresos por categoría (últimos 30 días)
        $ingresosPorCategoria = DB::table('ventas')
            ->join('venta_productos', 'ventas.id', '=', 'venta_productos.id_venta')
            ->join('productos', 'venta_productos.id_producto', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('ventas.fecha_venta', '>=', now()->subDays(30)->format('Y-m-d'))
            ->selectRaw('categorias.nombre as categoria, SUM(venta_productos.precio * venta_productos.cantidad) as total')
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderByDesc('total')
            ->get();

        // Tendencia diaria del mes actual
        $tendenciaMes = Venta::whereBetween('fecha_venta', [$inicioMesActual, $finMesActual])
            ->selectRaw('DATE(fecha_venta) as fecha, SUM(suma_total) as total, COUNT(*) as cantidad')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return response()->json([
            'success' => true,
            'rol' => 'contador',
            'estadisticas' => [
                'ingresos_mes_actual' => [
                    'total' => round($ingresosMesActual, 2),
                    'icon' => 'fas fa-dollar-sign', 'color' => 'success', 'titulo' => 'Ingresos ' . now()->locale('es')->isoFormat('MMMM'),
                ],
                'ingresos_mes_anterior' => [
                    'total' => round($ingresosMesAnterior, 2),
                    'icon' => 'fas fa-calendar-alt', 'color' => 'info', 'titulo' => 'Mes Anterior',
                ],
                'variacion' => [
                    'total' => $variacion,
                    'icon' => $variacion >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down',
                    'color' => $variacion >= 0 ? 'success' : 'danger',
                    'titulo' => 'Variación %',
                ],
                'transacciones_mes' => [
                    'total' => $transaccionesMes,
                    'icon' => 'fas fa-receipt', 'color' => 'primary', 'titulo' => 'Transacciones',
                ],
                'ticket_promedio' => [
                    'total' => $ticketPromedio,
                    'icon' => 'fas fa-tag', 'color' => 'warning', 'titulo' => 'Ticket Promedio',
                ],
                'total_ingresos' => [
                    'total' => round(Venta::sum('suma_total'), 2),
                    'icon' => 'fas fa-chart-line', 'color' => 'success', 'titulo' => 'Total Histórico',
                ],
            ],
            'datosGraficos' => [
                'ingresos_por_mes' => $ingresosPorMes,
                'ingresos_por_metodo' => $ingresosPorMetodo,
                'ingresos_por_categoria' => $ingresosPorCategoria,
                'tendencia_mes' => $tendenciaMes,
            ],
        ]);
    }

    // ─── ALERTAS Y VENTAS RECIENTES (compartidas) ────────────────────────────

    private function alertasSistema(): array
    {
        $alertas = [];
        $productosStockBajo = Producto::whereColumn('stock', '<', 'stock_minimo')
            ->select('nombre', 'stock', 'stock_minimo')
            ->orderBy('stock')
            ->limit(5)
            ->get();

        foreach ($productosStockBajo as $p) {
            $alertas[] = [
                'tipo' => $p->stock == 0 ? 'danger' : 'warning',
                'icon' => $p->stock == 0 ? 'fas fa-times-circle' : 'fas fa-exclamation-triangle',
                'mensaje' => $p->stock == 0
                    ? "{$p->nombre} — SIN STOCK"
                    : "{$p->nombre} — Stock bajo: {$p->stock} / mín {$p->stock_minimo}",
                'fecha' => now()->format('d/m H:i'),
            ];
        }
        return $alertas;
    }

    private function ventasRecientes()
    {
        return Venta::with(['cliente', 'ventaProductos.producto'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($v) {
                $detalle = $v->ventaProductos->first();
                if ($detalle?->producto) {
                    $v->setRelation('producto', $detalle->producto);
                }
                return $v;
            });
    }
}
