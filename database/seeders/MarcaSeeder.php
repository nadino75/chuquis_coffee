<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        // Los primeros 5 mantienen los IDs originales para compatibilidad
        // con ProveedorProductoSeeder (usa marca_id=1 y marca_id=5)
        $marcas = [
            ['nombre' => 'Starbucks'],        // ID 1
            ['nombre' => 'Orgánica Bolivia'], // ID 2 (corregido de 'Orignal')
            ['nombre' => 'Colcafe'],          // ID 3
            ['nombre' => 'Nespresso'],        // ID 4
            ['nombre' => 'Juan Valdez'],      // ID 5
            ['nombre' => 'Illy'],             // ID 6
            ['nombre' => 'Lavazza'],          // ID 7
        ];

        foreach ($marcas as $marca) {
            Marca::create($marca);
        }

        $this->command->info('7 marcas insertadas.');
    }
}
