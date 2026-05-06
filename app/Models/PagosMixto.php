<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagosMixto extends Model
{
    use HasFactory;

    protected $table = 'pagos_mixto';
    protected $perPage = 20;

    protected $fillable = ['recibo', 'fecha', 'tipo_pago', 'monto', 'pago_id'];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id', 'id');
    }
}
