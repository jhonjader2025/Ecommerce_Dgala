<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VarianteProducto extends Model
{
    use HasFactory;

    protected $table = 'variantes_producto';

    protected $fillable = [
        'producto_id',
        'lona_id',
        'sku',
        'color',
        'talla',
        'stock',
        'precio_extra',
        'eliminado_en'
    ];

    /**
     * RELACIÓN: La Variante pertenece a un Producto padre (Muchos a 1)
     * * ¿Qué hace? Si estás en el carrito procesando un SKU, te permite saber cuál es el nombre del producto principal.
     * Ejemplo en código: $variante->producto->nombre;
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * RELACIÓN: La Variante está asociada a una Lona de inventario (Muchos a 1)
     * * ¿Qué hace? Vincula esta opción de venta con la tela real. Gracias a esto, MySQL sabe a qué lona descontarle stock cuando se venda esta variante.
     * Ejemplo en código: $variante->lona->codigo;
     */
    public function lona(): BelongsTo
    {
        return $this->belongsTo(Lona::class, 'lona_id');
    }
}
