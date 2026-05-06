<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'ci' => '1234567', 'ci_complemento' => null, 'nit' => '1234567890123',
                'nombres' => 'Juan Carlos', 'apellido_paterno' => 'Pérez', 'apellido_materno' => 'Gómez',
                'sexo' => 'masculino', 'telefono' => '4641234', 'celular' => '71234567',
                'correo' => 'juan.perez@email.com',
            ],
            [
                'ci' => '7654321', 'ci_complemento' => 'A', 'nit' => null,
                'nombres' => 'María Elena', 'apellido_paterno' => 'Gutiérrez', 'apellido_materno' => 'López',
                'sexo' => 'femenino', 'telefono' => null, 'celular' => '76543210',
                'correo' => 'maria.gutierrez@email.com',
            ],
            [
                'ci' => '8912345', 'ci_complemento' => null, 'nit' => '8912345678901',
                'nombres' => 'Carlos Alberto', 'apellido_paterno' => 'Rodríguez', 'apellido_materno' => 'Martínez',
                'sexo' => 'masculino', 'telefono' => '4645678', 'celular' => '78901234',
                'correo' => 'carlos.rodriguez@email.com',
            ],
            [
                'ci' => '4561230', 'ci_complemento' => null, 'nit' => null,
                'nombres' => 'Ana Lucía', 'apellido_paterno' => 'Flores', 'apellido_materno' => 'Vargas',
                'sexo' => 'femenino', 'telefono' => null, 'celular' => '74561230',
                'correo' => 'ana.flores@email.com',
            ],
            [
                'ci' => '9876543', 'ci_complemento' => 'B', 'nit' => null,
                'nombres' => 'Pedro Rafael', 'apellido_paterno' => 'Mamani', 'apellido_materno' => 'Quispe',
                'sexo' => 'masculino', 'telefono' => '4649876', 'celular' => '79876543',
                'correo' => 'pedro.mamani@email.com',
            ],
            [
                'ci' => '3216549', 'ci_complemento' => null, 'nit' => '3216549012345',
                'nombres' => 'Luciana Beatriz', 'apellido_paterno' => 'Torres', 'apellido_materno' => 'Molina',
                'sexo' => 'femenino', 'telefono' => null, 'celular' => '73216549',
                'correo' => 'luciana.torres@email.com',
            ],
            [
                'ci' => '6543217', 'ci_complemento' => null, 'nit' => null,
                'nombres' => 'Miguel Ángel', 'apellido_paterno' => 'Condori', 'apellido_materno' => 'Huanca',
                'sexo' => 'masculino', 'telefono' => '4646543', 'celular' => '76543217',
                'correo' => 'miguel.condori@email.com',
            ],
            [
                'ci' => '2109876', 'ci_complemento' => null, 'nit' => null,
                'nombres' => 'Sofía Valentina', 'apellido_paterno' => 'Herrera', 'apellido_materno' => 'Salinas',
                'sexo' => 'femenino', 'telefono' => null, 'celular' => '72109876',
                'correo' => 'sofia.herrera@email.com',
            ],
        ];

        DB::table('clientes')->insert($clientes);
        $this->command->info('8 clientes de prueba insertados.');
    }
}
