<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProveedoresProducto
 *
 * @property $id
 * @property $proveedore_id
 * @property $producto_id
 * @property $cantidad
 * @property $precio
 * @property $fecha_compra
 * @property $fecha_vencimiento
 * @property $marca_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Marca $marca
 * @property Producto $producto
 * @property Proveedore $proveedore
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class ProveedoresProducto extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['proveedore_id', 'producto_id', 'cantidad', 'precio', 'fecha_compra', 'fecha_vencimiento', 'marca_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function marca()
    {
        return $this->belongsTo(\App\Models\Marca::class, 'marca_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producto()
    {
        return $this->belongsTo(\App\Models\Producto::class, 'producto_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proveedore()
    {
        return $this->belongsTo(\App\Models\Proveedore::class, 'proveedore_id', 'id');
    }
    
}
