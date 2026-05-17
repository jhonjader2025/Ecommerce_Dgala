<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LonaTalla extends Model
{
    use HasFactory;

    // Indica la tabla específica de MySQL para controlar el stock de tallas
    protected $table = 'lona_tallas';

    // Campos obligatorios para insertar el stock por talla
    protected $fillable = [
        'lona_id',
        'talla',
        'cantidad'
    ];

    /**
     * RELACIÓN: Este registro de Talla pertenece a una Lona (Muchos a 1)
     * * ¿Qué hace? Permite que si estás parado en una fila de stock (Ej: Talla M), sepas a qué lona exacta corresponde.
     * Ejemplo en código: $talla->lona->color; (Te dice de qué color es la lona a la que pertenece esta talla).
     */
    public function lona(): BelongsTo
    {
        return $this->belongsTo(Lona::class, 'lona_id');
    }
}
