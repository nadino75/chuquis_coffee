<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            ['nombre' => 'Starbucks'],
            ['nombre' => 'Orignal'],
            ['nombre' => 'Colcafe'],
            ['nombre' => 'Nespresso'],
            ['nombre' => 'Juan Valdez'],
        ];

        foreach ($marcas as $marca) {
            Marca::create($marca);
        }
    }
}