<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialLona extends Model
{
    use HasFactory;

    // Conexión exacta a la tabla de auditoría
    protected $table = 'historial_lonas';

    protected $fillable = [
        'lona_id',
        'orden_item_id',
        'accion',
        'talla',
        'cantidad_cambio',
        'cantidad_restante',
        'notas'
    ];

    /**
     * RELACIÓN: El registro del historial pertenece a una Lona (Muchos a 1)
     * * ¿Qué hace? Permite auditar qué tela fue la que sufrió la modificación de inventario.
     * Ejemplo en código: $historial->lona->codigo;
     */
    public function lona(): BelongsTo
    {
        return $this->belongsTo(Lona::class, 'lona_id');
    }
}
