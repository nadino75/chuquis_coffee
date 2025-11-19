<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Venta
 *
 * @property $id
 * @property $producto_id
 * @property $cliente_ci
 * @property $pago_id
 * @property $precio
 * @property $cantidad
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
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['producto_id', 'cliente_ci', 'pago_id', 'precio', 'cantidad'];


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
    
}
