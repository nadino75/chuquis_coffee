<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VentaProductoSeeder extends Seeder
{
    public function run(): void
    {
        $ventas = DB::table('ventas')->select('id', 'suma_total')->get();
        $productos = DB::table('productos')->select('id', 'precio')->get();

        if ($ventas->isEmpty() || $productos->isEmpty()) {
            return;
        }

        $detalles = [];

        foreach ($ventas as $index => $venta) {
            $producto = $productos[$index % $productos->count()];
            $cantidad = max(1, (int) floor($venta->suma_total / max((float) $producto->precio, 0.01)));

            $detalles[] = [
                'id_producto' => $producto->id,
                'id_venta' => $venta->id,
                'precio' => $producto->precio,
                'cantidad' => $cantidad,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('venta_productos')->insert($detalles);
    }
}
