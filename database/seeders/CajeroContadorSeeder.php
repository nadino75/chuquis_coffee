<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CajeroContadorSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ---------- Cajero ----------
        $cajero = Role::firstOrCreate(['name' => 'Cajero', 'guard_name' => 'web']);
        $cajero->syncPermissions([
            'ver-producto',
            'ver-cliente',
            'crear-cliente',
            'editar-cliente',
            'ver-venta',
            'crear-venta',
            'ver-pago',
            'crear-pago',
        ]);

        // ---------- Contador ----------
        $contador = Role::firstOrCreate(['name' => 'Contador', 'guard_name' => 'web']);
        $contador->syncPermissions([
            'ver-venta',
            'ver-pago',
            'ver-cliente',
            'ver-producto',
            'ver-categoria',
            'ver-proveedor',
            'ver-tipo',
            'ver-marca',
        ]);

        $this->command->info('Roles Cajero y Contador creados/actualizados correctamente.');
    }
}
