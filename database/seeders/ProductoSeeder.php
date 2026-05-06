<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        // NOTA: precio es decimal(3,2) → máximo 9.99
        // Productos con stock bajo/cero para probar las alertas del dashboard
        $productos = [
            // ── Café en Grano (categoria_id=1) ──────────────────────────────
            [
                'nombre'       => 'Café Arabica Premium',
                'descripcion'  => 'Café en grano 100% arábica de altura, tostado medio',
                'stock'        => 50,
                'stock_minimo' => 10,
                'precio'       => 4.50,
                'categoria_id' => 1,
                'imagen'       => null,
            ],
            // ── Café Molido (categoria_id=2) ─────────────────────────────────
            [
                'nombre'       => 'Café Molido Tradicional',
                'descripcion'  => 'Café molido de tueste oscuro para cafetera italiana',
                'stock'        => 30,
                'stock_minimo' => 8,
                'precio'       => 3.50,
                'categoria_id' => 2,
                'imagen'       => null,
            ],
            // ── Café en Grano (adicional) ────────────────────────────────────
            [
                'nombre'       => 'Café Orgánico Especial',
                'descripcion'  => 'Grano orgánico certificado, cosecha de temporada',
                'stock'        => 15,
                'stock_minimo' => 10,
                'precio'       => 5.00,
                'categoria_id' => 1,
                'imagen'       => null,
            ],
            // ── Café Molido (bajo stock) ─────────────────────────────────────
            [
                'nombre'       => 'Café Molido Espresso',
                'descripcion'  => 'Molido fino especial para máquinas espresso',
                'stock'        => 5,    // BAJO — stock < stock_minimo
                'stock_minimo' => 8,
                'precio'       => 3.80,
                'categoria_id' => 2,
                'imagen'       => null,
            ],
            // ── Bebidas Calientes (categoria_id=3) ───────────────────────────
            [
                'nombre'       => 'Espresso Simple',
                'descripcion'  => 'Shot de espresso de 30 ml, sabor concentrado',
                'stock'        => 100,
                'stock_minimo' => 20,
                'precio'       => 1.50,
                'categoria_id' => 3,
                'imagen'       => null,
            ],
            // ── Posición 6: Torta de Chocolate (compatible con ProveedorProductoSeeder) ──
            [
                'nombre'       => 'Torta de Chocolate',
                'descripcion'  => 'Porción artesanal de torta húmeda de chocolate',
                'stock'        => 20,
                'stock_minimo' => 5,
                'precio'       => 1.20,
                'categoria_id' => 5,
                'imagen'       => null,
            ],
            // ── Bebidas Calientes (continuación) ─────────────────────────────
            [
                'nombre'       => 'Capuchino Grande',
                'descripcion'  => 'Capuchino cremoso con leche vaporizada, 300 ml',
                'stock'        => 80,
                'stock_minimo' => 15,
                'precio'       => 2.20,
                'categoria_id' => 3,
                'imagen'       => null,
            ],
            [
                'nombre'       => 'Latte Macchiato',
                'descripcion'  => 'Leche vaporizada con doble shot de espresso',
                'stock'        => 60,
                'stock_minimo' => 15,
                'precio'       => 2.50,
                'categoria_id' => 3,
                'imagen'       => null,
            ],
            [
                'nombre'       => 'Americano',
                'descripcion'  => 'Espresso diluido en agua caliente, 250 ml',
                'stock'        => 90,
                'stock_minimo' => 20,
                'precio'       => 1.80,
                'categoria_id' => 3,
                'imagen'       => null,
            ],
            // ── Bebidas Calientes (sin stock — para probar alertas) ───────────
            [
                'nombre'       => 'Té Verde Importado',
                'descripcion'  => 'Té verde japonés matcha, preparación en caliente',
                'stock'        => 0,    // SIN STOCK
                'stock_minimo' => 5,
                'precio'       => 2.00,
                'categoria_id' => 3,
                'imagen'       => null,
            ],
            // ── Bebidas Calientes (bajo stock) ───────────────────────────────
            [
                'nombre'       => 'Chocolate Caliente',
                'descripcion'  => 'Bebida caliente de cacao puro con leche, 300 ml',
                'stock'        => 3,    // BAJO — stock < stock_minimo
                'stock_minimo' => 10,
                'precio'       => 2.20,
                'categoria_id' => 3,
                'imagen'       => null,
            ],
            // ── Bebidas Frías (categoria_id=4) ───────────────────────────────
            [
                'nombre'       => 'Frappé de Café',
                'descripcion'  => 'Bebida fría cremosa con hielo y café, 400 ml',
                'stock'        => 40,
                'stock_minimo' => 12,
                'precio'       => 2.80,
                'categoria_id' => 4,
                'imagen'       => null,
            ],
            [
                'nombre'       => 'Frappé de Caramelo',
                'descripcion'  => 'Frappé de café con sirope de caramelo y crema',
                'stock'        => 25,
                'stock_minimo' => 12,
                'precio'       => 3.00,
                'categoria_id' => 4,
                'imagen'       => null,
            ],
            // ── Pastelería (categoria_id=5) ───────────────────────────────────
            [
                'nombre'       => 'Croissant de Mantequilla',
                'descripcion'  => 'Croissant horneado diario, masa hojaldrada',
                'stock'        => 4,    // BAJO — stock < stock_minimo
                'stock_minimo' => 8,
                'precio'       => 1.50,
                'categoria_id' => 5,
                'imagen'       => null,
            ],
            // ── Pastelería (sin stock) ────────────────────────────────────────
            [
                'nombre'       => 'Muffin de Arándano',
                'descripcion'  => 'Muffin esponjoso con arándanos frescos',
                'stock'        => 0,    // SIN STOCK
                'stock_minimo' => 5,
                'precio'       => 1.30,
                'categoria_id' => 5,
                'imagen'       => null,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }

        $this->command->info('15 productos insertados (3 sin stock / bajo stock para alertas).');
    }
}
