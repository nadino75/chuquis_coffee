<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

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
}