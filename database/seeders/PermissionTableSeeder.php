<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder; 
use Spatie\Permission\Models\Permission;


class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $Permissions = [
            'crear-usuario',
            'ver-usuario',
            'editar-usuario',
            'borrar-usuario',
            'crear-proveedor',
            'ver-proveedor',
            'editar-proveedor',
            'borrar-proveedor',
            'ver-categoria',
            'crear-categoria',
            'editar-categoria',
            'borrar-categoria',
            'ver-producto',
            'crear-producto',
            'editar-producto',
            'borrar-producto',
            'ver-cliente',
            'crear-cliente',
            'editar-cliente',
            'borrar-cliente',
            'ver-venta',
            'crear-venta',
            'editar-venta',
            'borrar-venta',
            'ver-pago',
            'crear-pago',
            'editar-pago',
            'borrar-pago',
            'ver-tipo',
            'crear-tipo',
            'editar-tipo',
            'borrar-tipo',
            'ver-rol',
            'crear-rol',
            'editar-rol',
            'borrar-rol',
            'ver-marca',
            'crear-marca',
            'editar-marca',
            'borrar-marca',
        ];
        foreach ($Permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}