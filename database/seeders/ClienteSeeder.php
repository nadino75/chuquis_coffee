<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'ci' => '1234567',
                'NIT' => '1234567890123',
                'nombres' => 'Juan Carlos',
                'apellido_paterno' => 'Pérez',
                'apellido_materno' => 'Gómez',
                'sexo' => 'masculino',
                'telefono' => '4641234',
                'celular' => '71234567',
                'correo' => 'juan.perez@email.com',
            ],
            [
                'ci' => '7654321',
                'NIT' => null,
                'nombres' => 'María Elena',
                'apellido_paterno' => 'Gutiérrez',
                'apellido_materno' => 'López',
                'sexo' => 'femenino',
                'telefono' => null,
                'celular' => '76543210',
                'correo' => 'maria.gutierrez@email.com',
            ],
            [
                'ci' => '8912345',
                'NIT' => '8912345678901',
                'nombres' => 'Carlos Alberto',
                'apellido_paterno' => 'Rodríguez',
                'apellido_materno' => 'Martínez',
                'sexo' => 'masculino',
                'telefono' => '4645678',
                'celular' => '78901234',
                'correo' => 'carlos.rodriguez@email.com',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}