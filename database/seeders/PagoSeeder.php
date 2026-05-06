<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagoSeeder extends Seeder
{
    public function run(): void
    {
        $clientesCi = DB::table('clientes')->pluck('ci')->values()->toArray();

        if (empty($clientesCi)) {
            $this->command->error('No hay clientes. Ejecute ClienteSeeder primero.');
            return;
        }

        $nC  = count($clientesCi);
        $tipos = ['efectivo', 'efectivo', 'efectivo', 'efectivo', 'qr', 'qr', 'qr', 'tarjeta', 'tarjeta', 'transferencia', 'mixto'];
        $montos = [
            3.50, 5.60, 7.20, 8.90, 9.80, 11.40, 13.50, 15.80, 18.90, 21.00,
            24.50, 27.30, 6.70, 8.40, 4.80, 10.50, 12.90, 16.70, 19.20, 23.40,
            7.60, 5.20, 14.70, 9.60, 4.50, 8.10, 6.30, 17.80, 22.10, 31.00,
        ];
        $horasDia = ['08:15', '09:30', '10:45', '11:00', '12:20', '13:45', '14:30', '15:50', '16:15', '17:40'];

        $pagos = [];
        $idx   = 0;

        $add = function (string $fecha, string $hora) use (
            &$pagos, &$idx, $clientesCi, $tipos, $montos, $nC
        ): void {
            $pagos[] = [
                'recibo'        => 'RC-' . str_pad((string) ($idx + 1), 6, '0', STR_PAD_LEFT),
                'fecha'         => $fecha,
                'tipo_pago'     => $tipos[$idx % count($tipos)],
                'total_pagado'  => $montos[$idx % count($montos)],
                'cliente_ci'    => $clientesCi[$idx % $nC],
                'pago_mixto_id' => null,
                'created_at'    => $fecha . ' ' . $hora . ':00',
                'updated_at'    => now()->toDateTimeString(),
            ];
            $idx++;
        };

        // ── 5 meses atrás (~130–155 días) — 13 registros ────────────────────
        for ($d = 155; $d >= 130; $d -= 2) {
            $add(now()->subDays($d)->toDateString(), $horasDia[$idx % 10]);
        }

        // ── 4 meses atrás (~100–125 días) — 13 registros ────────────────────
        for ($d = 125; $d >= 100; $d -= 2) {
            $add(now()->subDays($d)->toDateString(), $horasDia[$idx % 10]);
        }

        // ── 3 meses atrás (~70–95 días) — 13 registros ──────────────────────
        for ($d = 95; $d >= 70; $d -= 2) {
            $add(now()->subDays($d)->toDateString(), $horasDia[$idx % 10]);
        }

        // ── 2 meses atrás (~40–65 días) — 13 registros ──────────────────────
        for ($d = 65; $d >= 40; $d -= 2) {
            $add(now()->subDays($d)->toDateString(), $horasDia[$idx % 10]);
        }

        // ── 1 mes atrás (~15–38 días) — 12 registros ────────────────────────
        for ($d = 38; $d >= 15; $d -= 2) {
            $add(now()->subDays($d)->toDateString(), $horasDia[$idx % 10]);
        }

        // ── Últimas 2 semanas (1–14 días atrás) — 14 registros ──────────────
        for ($d = 14; $d >= 1; $d--) {
            $add(now()->subDays($d)->toDateString(), $horasDia[$idx % 10]);
        }

        // ── HOY: 8 registros a horas distintas (gráfico por hora del Cajero) ─
        $horasHoy = ['08:10', '09:25', '10:40', '11:30', '12:15', '14:00', '15:20', '16:45'];
        foreach ($horasHoy as $hora) {
            $add(now()->toDateString(), $hora);
        }

        DB::table('pagos')->insert($pagos);
        $this->command->info(count($pagos) . ' pagos de prueba insertados (6 meses de histórico + hoy).');
    }
}
