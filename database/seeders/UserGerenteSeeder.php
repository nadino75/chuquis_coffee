<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserGerenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(){
        $user = User::create([
            'name' => 'gerente',
            'email' => 'gerente@gmail.com',
            'password' => bcrypt('12345678')
        ]);
        $role = Role::create(['name' => 'Gerente']);
        $permissions = Permission::whereIn('name', [
            'ver-venta',
            'crear-venta',
            'editar-venta',
            'borrar-venta',
            'ver-cliente',
            'crear-cliente',
            'editar-cliente',
            'borrar-cliente',
            'ver-pago',
            'crear-pago',
            'editar-pago',
            'borrar-pago',
            'ver-producto',
            'crear-producto',
            'editar-producto',
            'borrar-producto',
            'ver-categoria',
            'crear-categoria',
            'editar-categoria',
            'borrar-categoria',
            'ver-tipo',
            'crear-tipo',
            'editar-tipo',
            'borrar-tipo',
            'ver-usuario',
            'ver-proveedor',
            'ver-rol',
        ])->get();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);
    }
}
