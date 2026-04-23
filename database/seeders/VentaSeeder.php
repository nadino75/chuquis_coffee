<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VentaSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = DB::table('clientes')->select('id')->get();
        $pagos = DB::table('pagos')->select('id', 'fecha', 'total_pagado')->get();

        if ($clientes->isEmpty() || $pagos->isEmpty()) {
            return;
        }

        $ventas = [];

        foreach ($pagos as $index => $pago) {
            $cliente = $clientes[$index % $clientes->count()];

            $ventas[] = [
                'fecha_venta' => $pago->fecha,
                'suma_total' => $pago->total_pagado,
                'cliente_id' => $cliente->id,
                'pago_id' => $pago->id,
                'detalles' => 'Venta de prueba #' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('ventas')->insert($ventas);
    }
}
