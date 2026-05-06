<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Cliente;
use App\Models\Pago;

class Venta extends Model
{
    use HasFactory;
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fecha_venta',
        'suma_total',
        'cliente_id',
        'pago_id',
        'detalles',
    ];

    protected $casts = [
        'fecha_venta' => 'date',
        'suma_total' => 'decimal:2',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pago()
    {
        return $this->belongsTo(\App\Models\Pago::class, 'pago_id', 'id');
    }

    public function ventaProductos()
    {
        return $this->hasMany(\App\Models\VentaDetalle::class, 'id_venta', 'id');
    }

    public function productos()
    {
        return $this->belongsToMany(
            \App\Models\Producto::class,
            'venta_productos',
            'id_venta',
            'id_producto'
        )->withPivot(['precio', 'cantidad'])->withTimestamps();
    }
}