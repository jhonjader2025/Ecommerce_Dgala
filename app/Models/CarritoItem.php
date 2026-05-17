<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarritoItem extends Model
{
    use HasFactory;

    protected $table = 'carrito_items';

    protected $fillable = [
        'carrito_id',
        'variante_id',
        'cantidad'
    ];

    /**
     * RELACIÓN: El Ítem pertenece a un Carrito padre (Muchos a 1)
     * * ¿Qué hace? Te regresa al contenedor principal del carrito.
     * Ejemplo en código: $item->carrito;
     */
    public function carrito(): BelongsTo
    {
        return $this->belongsTo(Carrito::class, 'carrito_id');
    }

    /**
     * RELACIÓN: El Ítem está amarrado a una Variante de Producto (Muchos a 1)
     * * ¿Qué hace? Te permite saber exactamente qué uniforme, qué talla y qué color metió el cliente en esta fila.
     * Ejemplo en código: $item->variante->sku;
     */
    public function variante(): BelongsTo
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_id');
    }
}
