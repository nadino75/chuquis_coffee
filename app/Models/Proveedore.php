<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedore extends Model
{
    use HasFactory;

    protected $perPage = 20;

    protected $fillable = ['nombre', 'contacto', 'telefono', 'celular', 'email', 'correo', 'direccion', 'ruc'];
}
