<?php

namespace Database\Seeders;

use App\Models\ProveedoresProducto;
use Illuminate\Database\Seeder;

class ProveedorProductoSeeder extends Seeder
{
    public function run(): void
    {
        $proveedorProductos = [
            [
                'proveedore_id' => 1,
                'producto_id' => 1,
                'cantidad' => 100,
                'precio' => 3500.00,
                'fecha_compra' => '2025-01-15',
                'fecha_vencimiento' => '2026-01-15',
                'marca_id' => 5, // Local
            ],
            [
                'proveedore_id' => 2,
                'producto_id' => 2,
                'cantidad' => 50,
                'precio' => 2800.00,
                'fecha_compra' => '2025-01-10',
                'fecha_vencimiento' => '2025-12-10',
                'marca_id' => 1, // Starbucks
            ],
            [
                'proveedore_id' => 3,
                'producto_id' => 6,
                'cantidad' => 25,
                'precio' => 8.00,
                'fecha_compra' => '2025-01-20',
                'fecha_vencimiento' => '2025-02-20',
                'marca_id' => 5, // Local
            ],
        ];

        foreach ($proveedorProductos as $proveedorProducto) {
            ProveedoresProducto::create($proveedorProducto);
        }
    }
}