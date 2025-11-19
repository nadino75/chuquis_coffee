<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PagosMixto
 *
 * @property $id
 * @property $tipo_pago
 * @property $monto
 * @property $pago_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Pago $pago
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class PagosMixto extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['tipo_pago', 'monto', 'pago_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pago()
    {
        return $this->belongsTo(\App\Models\Pago::class, 'pago_id', 'id');
    }
    
}
