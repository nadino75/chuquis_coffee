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
        'categoria_id',
        'marca_id',
        'imagen'
    ];

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
}