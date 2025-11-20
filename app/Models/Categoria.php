<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


/**
 * Class Categoria
 *
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property int|null $tipo_id
 * @property int|null $categoria_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property Categoria|null $categoria_padre
 * @property Tipo|null $tipo
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Categoria extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo_id',
        'categoria_id'
    ];

    /**
     * Relación con la categoría padre (auto-relación).
     *
     * @return BelongsTo
     */
    public function categoria_padre(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }

    /**
     * Relación con el tipo de producto.
     *
     * @return BelongsTo
     */
    public function tipo(): BelongsTo
    {
        return $this->belongsTo(Tipo::class, 'tipo_id', 'id');
    }
}
