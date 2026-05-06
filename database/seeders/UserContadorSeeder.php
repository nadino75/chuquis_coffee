<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserContadorSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'Contador')->first();

        if (!$role) {
            $this->command->error('Rol Contador no encontrado. Ejecute CajeroContadorSeeder primero.');
            return;
        }

        $user = User::create([
            'name'     => 'contador',
            'email'    => 'contador@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        $user->assignRole($role);
        $this->command->info('Usuario contador@gmail.com creado con rol Contador.');
    }
}
