<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserVistasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(){
        $user = User::create([
            'name' => 'vistas',
            'email' => 'vistas@gmail.com',
            'password' => bcrypt('12345678')
        ]);
        $role = Role::create(['name' => 'Vistas']);
        $permissions = Permission::whereIn('name', [
            'ver-venta',
            'ver-cliente',
            'ver-pago',
            'ver-producto',
            'ver-categoria',
            'ver-tipo',
            'ver-usuario',
            'ver-proveedor',
            'ver-rol'
        ])->get();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);
    }
}
