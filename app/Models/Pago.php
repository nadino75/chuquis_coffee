<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'recibo',
        'fecha',
        'tipo_pago',
        'total_pagado',
        'monto_recibido',
        'cambio',
        'cliente_ci',
        'pago_mixto_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total_pagado' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
        'cambio' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_ci', 'ci');
    }

    public function pagoPadre()
    {
        return $this->belongsTo(Pago::class, 'pago_mixto_id', 'id');
    }

    public function pagosHijos()
    {
        return $this->hasMany(Pago::class, 'pago_mixto_id', 'id');
    }

    protected $with = ['pagosHijos'];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'pago_id', 'id');
    }
}