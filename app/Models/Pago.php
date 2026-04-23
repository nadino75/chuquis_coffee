<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class Pago extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'recibo',
        'fecha',
        'tipo_pago',
        'total_pagado',
        'cliente_ci',
        'pago_mixto_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total_pagado' => 'decimal:2',
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

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'pago_id', 'id');
    }
}