<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagoSeeder extends Seeder
{
    public function run(): void
    {
        $clientesCi = DB::table('clientes')->pluck('ci')->values();

        if ($clientesCi->isEmpty()) {
            return;
        }

        $pagos = [
            [
                'recibo' => 'RC-240001',
                'fecha' => now()->subDays(6)->toDateString(),
                'tipo_pago' => 'efectivo',
                'total_pagado' => 16.80,
                'cliente_ci' => $clientesCi->get(0),
                'pago_mixto_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'recibo' => 'RC-240002',
                'fecha' => now()->subDays(5)->toDateString(),
                'tipo_pago' => 'tarjeta',
                'total_pagado' => 9.40,
                'cliente_ci' => $clientesCi->get(1) ?? $clientesCi->get(0),
                'pago_mixto_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'recibo' => 'RC-240003',
                'fecha' => now()->subDays(4)->toDateString(),
                'tipo_pago' => 'qr',
                'total_pagado' => 22.10,
                'cliente_ci' => $clientesCi->get(2) ?? $clientesCi->get(0),
                'pago_mixto_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'recibo' => 'RC-240004',
                'fecha' => now()->subDays(3)->toDateString(),
                'tipo_pago' => 'transferencia',
                'total_pagado' => 11.50,
                'cliente_ci' => $clientesCi->get(0),
                'pago_mixto_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'recibo' => 'RC-240005',
                'fecha' => now()->subDays(2)->toDateString(),
                'tipo_pago' => 'mixto',
                'total_pagado' => 18.90,
                'cliente_ci' => $clientesCi->get(1) ?? $clientesCi->get(0),
                'pago_mixto_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'recibo' => 'RC-240006',
                'fecha' => now()->subDay()->toDateString(),
                'tipo_pago' => 'efectivo',
                'total_pagado' => 14.25,
                'cliente_ci' => $clientesCi->get(2) ?? $clientesCi->get(0),
                'pago_mixto_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'recibo' => 'RC-240007',
                'fecha' => now()->toDateString(),
                'tipo_pago' => 'qr',
                'total_pagado' => 27.35,
                'cliente_ci' => $clientesCi->get(0),
                'pago_mixto_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('pagos')->insert($pagos);
    }
}
