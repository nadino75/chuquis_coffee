<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'ci',
        'ci_complemento',
        'nit',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'sexo',
        'telefono',
        'celular',
        'correo'
    ];

    protected $casts = [
        'id' => 'integer',
        'ci' => 'string',
        'nit' => 'string',
    ];
    
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cliente_id', 'id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'cliente_ci', 'ci');
    }
}

