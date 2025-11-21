<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class Cliente extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $primaryKey = 'ci';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ci',
        'NIT',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'sexo',
        'telefono',
        'celular',
        'correo'
    ];

    protected $casts = [
        'ci' => 'string',
        'NIT' => 'string',
    ];
    
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cliente_ci', 'ci');
    }
}

