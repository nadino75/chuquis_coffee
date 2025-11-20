<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Tipo;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Café en Grano',
                'descripcion' => 'Diferentes variedades de café en grano',
                'tipo_id' => 5, // Granos
            ],
            [
                'nombre' => 'Café Molido',
                'descripcion' => 'Café molido listo para preparar',
                'tipo_id' => 5, // Granos
            ],
            [
                'nombre' => 'Bebidas Calientes',
                'descripcion' => 'Bebidas preparadas con café',
                'tipo_id' => 1, // Bebidas
            ],
            [
                'nombre' => 'Bebidas Frías',
                'descripcion' => 'Bebidas refrescantes con café',
                'tipo_id' => 1, // Bebidas
            ],
            [
                'nombre' => 'Pastelería',
                'descripcion' => 'Postres y pasteles',
                'tipo_id' => 2, // Alimentos
            ],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}