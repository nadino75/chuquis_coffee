<?php

namespace Database\Seeders;

use App\Models\Tipo;
use Illuminate\Database\Seeder;

class TipoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Bebidas'],
            ['nombre' => 'Alimentos'],
            ['nombre' => 'Snacks'],
            ['nombre' => 'Accesorios'],
            ['nombre' => 'Granos'],
        ];

        foreach ($tipos as $tipo) {
            Tipo::create($tipo);
        }
    }
}