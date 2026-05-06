<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserCajeroSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'Cajero')->first();

        if (!$role) {
            $this->command->error('Rol Cajero no encontrado. Ejecute CajeroContadorSeeder primero.');
            return;
        }

        $user = User::create([
            'name'     => 'cajero',
            'email'    => 'cajero@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        $user->assignRole($role);
        $this->command->info('Usuario cajero@gmail.com creado con rol Cajero.');
    }
}
