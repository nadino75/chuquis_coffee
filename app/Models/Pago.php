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