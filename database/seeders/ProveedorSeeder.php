<?php

namespace Database\Seeders;

use App\Models\Proveedore;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            [
                'nombre' => 'Distribuidora Café Bolivia S.A.',
                'direccion' => 'Av. América #123, Sucre',
                'telefono' => '4641000',
                'celular' => '77001000',
                'correo' => 'ventas@cafebolivia.com',
            ],
            [
                'nombre' => 'Importadora Los Andes',
                'direccion' => 'Calle Junín #456, Sucre',
                'telefono' => '4642000',
                'celular' => '77002000',
                'correo' => 'contacto@importadoraandes.com',
            ],
            [
                'nombre' => 'Productos Naturales Chuquisaca',
                'direccion' => 'Av. Venezuela #789, Sucre',
                'telefono' => null,
                'celular' => '77003000',
                'correo' => 'info@productosnaturales.com',
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedore::create($proveedor);
        }
    }
}