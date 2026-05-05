<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminDashboardController extends Controller
{
    public function obtenerDatosAdmin()
    {
        try {
            return response()->json([
                'success' => true,
                'estadisticas' => $this->estadisticasAdmin(),
                'alertas' => $this->alertasSistema(),
                'ventasRecientes' => $this->ventasRecientes(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error en admin dashboard: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function estadisticasAdmin(): array
    {
        $hoy = now()->format('Y-m-d');

        return [
            'total_usuarios' => [
                'total' => User::count(),
                'icon' => 'fas fa-users-cog',
                'color' => 'primary',
                'titulo' => 'Usuarios Activos',
            ],
            'total_roles' => [
                'total' => Role::count(),
                'icon' => 'fas fa-user-shield',
                'color' => 'warning',
                'titulo' => 'Roles Configurados',
            ],
            'total_clientes' => [
                'total' => Cliente::count(),
                'icon' => 'fas fa-user-friends',
                'color' => 'info',
                'titulo' => 'Clientes',
            ],
            'total_productos' => [
                'total' => Producto::count(),
                'icon' => 'fas fa-box',
                'color' => 'success',
                'titulo' => 'Productos',
            ],
            'ventas_hoy' => [
                'total' => Venta::whereDate('fecha_venta', $hoy)->count(),
                'ingresos' => Venta::whereDate('fecha_venta', $hoy)->sum('suma_total'),
                'icon' => 'fas fa-cash-register',
                'color' => 'info',
                'titulo' => 'Ventas Hoy',
            ],
            'ingresos_totales' => [
                'total' => round(Venta::sum('suma_total'), 2),
                'icon' => 'fas fa-dollar-sign',
                'color' => 'success',
                'titulo' => 'Ingresos Totales',
            ],
        ];
    }

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
