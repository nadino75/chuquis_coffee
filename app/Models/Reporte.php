<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reporte extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'filtros',
        'datos',
        'configuracion',
        'usuario_id'
    ];

    protected $casts = [
        'filtros' => 'array',
        'datos' => 'array',
        'configuracion' => 'array'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}