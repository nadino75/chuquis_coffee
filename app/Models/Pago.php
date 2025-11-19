<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = ['recibo', 'fecha', 'tipo_pago', 'total_pagado', 'cliente_ci'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_ci', 'ci');
    }

    public function pagosMixtos()
    {
        return $this->hasMany(PagoMixto::class);
    }

    public function venta()
    {
        return $this->hasOne(Venta::class);
    }
}