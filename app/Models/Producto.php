<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Producto extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nombre', 
        'descripcion', 
        'precio', 
        'stock',
        'stock_minimo', // AGREGADO
        'categoria_id',
        'marca_id',
        'imagen'
    ];

    protected $casts = [
        'precio' => 'decimal:2', // AGREGADO
        'stock' => 'integer', // AGREGADO
        'stock_minimo' => 'integer' // AGREGADO
    ];

    // Boot method para establecer valores por defecto
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($producto) {
            // Establecer stock_minimo por defecto si no se proporciona
            if (empty($producto->stock_minimo)) {
                $producto->stock_minimo = 5;
            }
        });
    }

    // Relación con categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    // Relación con ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    // Método para reducir stock
    public function reducirStock($cantidad)
    {
        if ($this->stock < $cantidad) {
            throw new \Exception("Stock insuficiente para {$this->nombre}. Stock disponible: {$this->stock}");
        }
        
        $this->stock -= $cantidad;
        return $this->save();
    }

    // Método para aumentar stock
    public function aumentarStock($cantidad)
    {
        $this->stock += $cantidad;
        return $this->save();
    }

    // Scope para productos con stock
    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Scope para productos sin stock
    public function scopeSinStock($query)
    {
        return $query->where('stock', '<=', 0);
    }

    // Scope para productos con stock bajo (NUEVO)
    public function scopeStockBajo($query)
    {
        return $query->where('stock', '<', \DB::raw('stock_minimo'))
                    ->where('stock', '>', 0);
    }

    // Scope para productos que necesitan reorden (NUEVO)
    public function scopeNecesitaReorden($query)
    {
        return $query->where('stock', '<=', \DB::raw('stock_minimo'));
    }

    // Verificar si el stock está bajo (NUEVO)
    public function getStockBajoAttribute()
    {
        return $this->stock < $this->stock_minimo;
    }

    // Verificar si no hay stock (NUEVO)
    public function getSinStockAttribute()
    {
        return $this->stock == 0;
    }

    // Obtener el nivel de stock como porcentaje (NUEVO)
    public function getNivelStockAttribute()
    {
        if ($this->stock_minimo == 0) return 100;
        
        $nivel = ($this->stock / $this->stock_minimo) * 100;
        return min(100, $nivel); // Máximo 100%
    }

    // Obtener la clase CSS para el nivel de stock (NUEVO)
    public function getClaseNivelStockAttribute()
    {
        if ($this->sin_stock) {
            return 'danger';
        } elseif ($this->stock_bajo) {
            return 'warning';
        } else {
            return 'success';
        }
    }

    // Calcular el valor total del inventario para este producto (NUEVO)
    public function getValorInventarioAttribute()
    {
        return $this->precio * $this->stock;
    }

    // Método para verificar si se puede vender (NUEVO)
    public function sePuedeVender($cantidad = 1)
    {
        return $this->stock >= $cantidad;
    }

    // Método para obtener productos populares (NUEVO)
    public static function populares($limit = 10)
    {
        return static::withCount('ventas')
                    ->orderBy('ventas_count', 'desc')
                    ->limit($limit)
                    ->get();
    }
}