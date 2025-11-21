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

        return view('reportes.index', compact('datosReporte', 'fechaInicio', 'fechaFin', 'tipoReporte'));
    }

    /**
     * Método para mostrar reportes guardados (si es necesario)
     */
    public function mostrarReporteGuardado($id = null)
    {
        // Si tienes una tabla de reportes guardados, implementa aquí la lógica
        // Por ahora, redirigimos al index principal
        return redirect()->route('reportes.index')->with('info', 'Función de reportes guardados no implementada aún.');
    }

    /**
     * Método alternativo - si necesitas una página específica para reportes guardados
     */
    public function reportesGuardados()
    {
        // Aquí puedes implementar la lógica para mostrar reportes guardados
        return view('reportes.guardados', [
            'mensaje' => 'Módulo de reportes guardados - En desarrollo'
        ]);
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
            \Log::info("=== INICIANDO REPORTE DASHBOARD ===");
            
            // CORREGIR: Usar Carbon para manejo preciso de fechas
            $fechaInicioCarbon = Carbon::parse($fechaInicio)->startOfDay();
            $fechaFinCarbon = Carbon::parse($fechaFin)->endOfDay();

            \Log::info("Fechas corregidas - Inicio: {$fechaInicioCarbon}, Fin: {$fechaFinCarbon}");

            // VERIFICACIÓN DETALLADA CON FECHAS CORREGIDAS
            $totalVentas = Venta::whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon])->count();
            $ventasEnRango = Venta::with('producto')
                ->whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon])
                ->get();

            \Log::info("VENTAS en rango: {$totalVentas}");
            foreach ($ventasEnRango as $venta) {
                \Log::info("-> Venta ID: {$venta->id}, Fecha: {$venta->fecha_venta}, Producto: " . ($venta->producto ? $venta->producto->nombre : 'N/A') . ", Total: {$venta->total}");
            }

            $totalIngresos = Venta::whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon])->sum('total');
            $totalPagos = Pago::whereBetween('fecha', [$fechaInicioCarbon, $fechaFinCarbon])->count();
            $totalClientes = Cliente::count();
            $totalProductos = Producto::count();
            
            \Log::info("Estadísticas calculadas", [
                'ventas' => $totalVentas,
                'ingresos' => $totalIngresos,
                'pagos' => $totalPagos
            ]);

            // VENTAS POR DÍA - USAR RANGO COMPLETO SOLICITADO
            $ventasUltimaSemana = Venta::whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon])
                ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(total) as total')
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            \Log::info("Ventas por día resultados:", $ventasUltimaSemana->toArray());

            // PRODUCTOS MÁS VENDIDOS - CON FECHAS CORREGIDAS
            $productosMasVendidos = Venta::whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon])
                ->join('productos', 'ventas.producto_id', '=', 'productos.id')
                ->selectRaw('
                    productos.id, 
                    productos.nombre, 
                    COALESCE(SUM(ventas.cantidad), 0) as cantidad_vendida, 
                    COALESCE(SUM(ventas.total), 0) as total_ingresos
                ')
                ->groupBy('productos.id', 'productos.nombre')
                ->orderBy('cantidad_vendida', 'desc')
                ->limit(10)
                ->get();

            \Log::info("Productos más vendidos:", $productosMasVendidos->toArray());

            // ALERTAS DE STOCK
            $alertasStock = Producto::where('stock', '<', 10)
                ->select('id', 'nombre', 'stock', 'stock_minimo')
                ->orderBy('stock', 'asc')
                ->get();

            \Log::info("Alertas de stock:", $alertasStock->toArray());

            // MÉTODOS DE PAGO - CON FECHAS CORREGIDAS
            $metodosPago = Pago::whereBetween('fecha', [$fechaInicioCarbon, $fechaFinCarbon])
                ->selectRaw('tipo_pago as metodo_pago, COUNT(*) as cantidad, SUM(total_pagado) as monto_total')
                ->groupBy('tipo_pago')
                ->orderBy('cantidad', 'desc')
                ->get();

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
                'debug' => [
                    'fechas_originales' => ['inicio' => $fechaInicio, 'fin' => $fechaFin],
                    'fechas_corregidas' => ['inicio' => $fechaInicioCarbon->format('Y-m-d H:i:s'), 'fin' => $fechaFinCarbon->format('Y-m-d H:i:s')],
                    'ventas_en_rango_count' => $ventasEnRango->count(),
                ]
            ];

            \Log::info("=== DATOS FINALES DEL REPORTE ===");
            \Log::info(json_encode($datos, JSON_PRETTY_PRINT));

            return $datos;

        } catch (\Exception $e) {
            \Log::error('Error en reporte dashboard: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return [
                'dashboard' => true,
                'estadisticas_generales' => [
                    'total_ventas' => 0,
                    'total_ingresos' => 0,
                    'total_pagos' => 0,
                    'total_clientes' => Cliente::count(),
                    'total_productos' => Producto::count(),
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

            // CORREGIR FECHAS
            $fechaInicioCarbon = Carbon::parse($fechaInicio)->startOfDay();
            $fechaFinCarbon = Carbon::parse($fechaFin)->endOfDay();

            // Ventas por día
            $ventasPorDia = Venta::whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon])
                ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(total) as total')
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            \Log::info("Ventas por día", ['data' => $ventasPorDia->toArray()]);

            // Productos más vendidos
            $productosMasVendidos = Venta::whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon])
                ->join('productos', 'ventas.producto_id', '=', 'productos.id')
                ->selectRaw('productos.id, productos.nombre, SUM(ventas.cantidad) as cantidad_vendida, SUM(ventas.total) as total_ingresos')
                ->groupBy('productos.id', 'productos.nombre')
                ->orderBy('cantidad_vendida', 'desc')
                ->get();

            return [
                'ventas_por_dia' => $ventasPorDia,
                'productos_mas_vendidos' => $productosMasVendidos,
                'total_ventas' => Venta::whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon])->count(),
                'total_ingresos' => Venta::whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon])->sum('total'),
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

            // CORREGIR FECHAS
            $fechaInicioCarbon = Carbon::parse($fechaInicio)->startOfDay();
            $fechaFinCarbon = Carbon::parse($fechaFin)->endOfDay();

            // Pagos por día
            $pagosPorDia = Pago::whereBetween('fecha', [$fechaInicioCarbon, $fechaFinCarbon])
                ->selectRaw('DATE(fecha) as fecha, COUNT(*) as cantidad, SUM(total_pagado) as monto_total')
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();

            // Métodos de pago
            $metodosPago = Pago::whereBetween('fecha', [$fechaInicioCarbon, $fechaFinCarbon])
                ->selectRaw('tipo_pago as metodo_pago, COUNT(*) as cantidad, SUM(total_pagado) as monto_total')
                ->groupBy('tipo_pago')
                ->orderBy('cantidad', 'desc')
                ->get();

            return [
                'pagos_por_dia' => $pagosPorDia,
                'metodos_pago' => $metodosPago,
                'total_pagos' => Pago::whereBetween('fecha', [$fechaInicioCarbon, $fechaFinCarbon])->count(),
                'monto_total' => Pago::whereBetween('fecha', [$fechaInicioCarbon, $fechaFinCarbon])->sum('total_pagado'),
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
            // CORREGIR FECHAS
            $fechaInicioCarbon = Carbon::parse($fechaInicio)->startOfDay();
            $fechaFinCarbon = Carbon::parse($fechaFin)->endOfDay();

            // Productos más vendidos
            $productosMasVendidos = Venta::whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon])
                ->join('productos', 'ventas.producto_id', '=', 'productos.id')
                ->selectRaw('productos.id, productos.nombre, SUM(ventas.cantidad) as cantidad_vendida, SUM(ventas.total) as total_ingresos')
                ->groupBy('productos.id', 'productos.nombre')
                ->orderBy('cantidad_vendida', 'desc')
                ->get();

            // Productos por categoría
            $productosPorCategoria = DB::table('productos')
                ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                ->selectRaw('categorias.nombre as categoria, COUNT(*) as cantidad')
                ->groupBy('categorias.id', 'categorias.nombre')
                ->get();

            // Alertas de stock
            $alertasStock = Producto::where('stock', '<', 10)
                ->select('id', 'nombre', 'stock', 'stock_minimo')
                ->orderBy('stock', 'asc')
                ->get();

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
            $alertasStock = Producto::where('stock', '<', 10)
                ->select('id', 'nombre', 'stock', 'stock_minimo')
                ->orderBy('stock', 'asc')
                ->get();

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
            // CORREGIR FECHAS
            $fechaInicioCarbon = Carbon::parse($fechaInicio)->startOfDay();
            $fechaFinCarbon = Carbon::parse($fechaFin)->endOfDay();

            // Mejores clientes
            $mejoresClientes = Cliente::withCount(['ventas' => function($query) use ($fechaInicioCarbon, $fechaFinCarbon) {
                $query->whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon]);
            }])
            ->withSum(['ventas' => function($query) use ($fechaInicioCarbon, $fechaFinCarbon) {
                $query->whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon]);
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
            $clientesNuevos = Cliente::whereBetween('created_at', [$fechaInicioCarbon, $fechaFinCarbon])->count();

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

    // MÉTODOS AUXILIARES CORREGIDOS
    private function obtenerProductosMasVendidos($fechaInicio, $fechaFin)
    {
        try {
            // CORREGIR FECHAS
            $fechaInicioCarbon = Carbon::parse($fechaInicio)->startOfDay();
            $fechaFinCarbon = Carbon::parse($fechaFin)->endOfDay();

            $resultado = Venta::whereBetween('fecha_venta', [$fechaInicioCarbon, $fechaFinCarbon])
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
        // CORREGIR FECHAS
        $fechaInicioCarbon = Carbon::parse($fechaInicio)->startOfDay();
        $fechaFinCarbon = Carbon::parse($fechaFin)->endOfDay();

        $metodos = Pago::whereBetween('fecha', [$fechaInicioCarbon, $fechaFinCarbon])
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

        // Usar la fachada PDF correctamente
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

    /**
     * Método para debug del sistema
     */
    public function debug()
    {
        $datos = [
            'ventas_count' => Venta::count(),
            'pagos_count' => Pago::count(),
            'productos_count' => Producto::count(),
            'clientes_count' => Cliente::count(),
            'tablas_existentes' => DB::select('SHOW TABLES'),
            'fecha_servidor' => now()->format('Y-m-d H:i:s')
        ];

        return response()->json($datos);
    }
}