<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    use HasFactory;

    protected $table = 'venta_productos';
    
    protected $fillable = [
        'id_producto',
        'id_venta',
        'precio',
        'cantidad',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'cantidad' => 'integer',
    ];

    // Relación con venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id');
    }

    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id');
    }
}