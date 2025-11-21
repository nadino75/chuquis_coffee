<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Producto;

/**
 * Class Venta
 *
 * @property $id
 * @property $producto_id
 * @property $cliente_ci
 * @property $pago_id
 * @property $precio
 * @property $cantidad
 * @property $fecha_venta
 * @property $total
 * @property $created_at
 * @property $updated_at
 *
 * @property Cliente $cliente
 * @property Pago $pago
 * @property Producto $producto
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Venta extends Model
{
    use HasFactory, HasRoles;
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'producto_id', 
        'cliente_ci', 
        'pago_id', 
        'precio', 
        'cantidad',
        'fecha_venta',
        'total'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_venta' => 'date'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_ci', 'ci');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pago()
    {
        return $this->belongsTo(\App\Models\Pago::class, 'pago_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producto()
    {
        return $this->belongsTo(\App\Models\Producto::class, 'producto_id', 'id');
    }

    // Calcular el total automáticamente
    public function getSubtotalAttribute()
    {
        return $this->precio * $this->cantidad;
    }
}