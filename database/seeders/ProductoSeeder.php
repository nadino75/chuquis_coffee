<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Café Arabica Premium',
                'stock' => 50,
                'precio' => 45.00,
                'categoria_id' => 1, // Café en Grano
            ],
            [
                'nombre' => 'Café Molido Tradicional',
                'stock' => 30,
                'precio' => 35.00,
                'categoria_id' => 2, // Café Molido
            ],
            [
                'nombre' => 'Espresso Doble',
                'stock' => 100,
                'precio' => 15.00,
                'categoria_id' => 3, // Bebidas Calientes
            ],
            [
                'nombre' => 'Capuchino Grande',
                'stock' => 80,
                'precio' => 18.00,
                'categoria_id' => 3, // Bebidas Calientes
            ],
            [
                'nombre' => 'Frappé de Café',
                'stock' => 60,
                'precio' => 22.00,
                'categoria_id' => 4, // Bebidas Frías
            ],
            [
                'nombre' => 'Torta de Chocolate',
                'stock' => 20,
                'precio' => 12.00,
                'categoria_id' => 5, // Pastelería
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}