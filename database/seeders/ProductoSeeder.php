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
                'descripcion' => 'Café en grano de alta calidad',
                'stock' => 50,
                'stock_minimo' => 10,
                'precio' => 4.50,
                'categoria_id' => 1, // Café en Grano
                'imagen' => null,
            ],
            [
                'nombre' => 'Café Molido Tradicional',
                'descripcion' => 'Café molido para preparación tradicional',
                'stock' => 30,
                'stock_minimo' => 8,
                'precio' => 3.50,
                'categoria_id' => 2, // Café Molido
                'imagen' => null,
            ],
            [
                'nombre' => 'Espresso Doble',
                'descripcion' => 'Bebida caliente con doble carga de café',
                'stock' => 100,
                'stock_minimo' => 20,
                'precio' => 1.50,
                'categoria_id' => 3, // Bebidas Calientes
                'imagen' => null,
            ],
            [
                'nombre' => 'Capuchino Grande',
                'descripcion' => 'Capuchino cremoso en presentación grande',
                'stock' => 80,
                'stock_minimo' => 15,
                'precio' => 1.80,
                'categoria_id' => 3, // Bebidas Calientes
                'imagen' => null,
            ],
            [
                'nombre' => 'Frappé de Café',
                'descripcion' => 'Bebida fría de café con hielo',
                'stock' => 60,
                'stock_minimo' => 12,
                'precio' => 2.20,
                'categoria_id' => 4, // Bebidas Frías
                'imagen' => null,
            ],
            [
                'nombre' => 'Torta de Chocolate',
                'descripcion' => 'Porción de torta de chocolate artesanal',
                'stock' => 20,
                'stock_minimo' => 5,
                'precio' => 1.20,
                'categoria_id' => 5, // Pastelería
                'imagen' => null,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}