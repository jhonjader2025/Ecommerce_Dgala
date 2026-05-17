<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenItem extends Model
{
    use HasFactory;

    protected $table = 'orden_items';

    protected $fillable = [
        'orden_id',
        'variante_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    /**
     * RELACIÓN: El Ítem pertenece a una Orden padre (Muchos a 1)
     * * ¿Qué hace? Te regresa a la cabecera del pedido principal.
     */
    public function orden(): BelongsTo
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    /**
     * RELACIÓN: El Ítem pertenece a una Variante de Producto (Muchos a 1)
     * * ¿Qué hace? Te dice exactamente qué uniforme físico (con su talla y color) se empacó en este renglón del pedido.
     * Ejemplo en código: $item->variante->sku;
     */
    public function variante(): BelongsTo
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_id');
    }
}
