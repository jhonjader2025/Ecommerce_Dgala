<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoDetalle extends Model
{
    use HasFactory;

    // Le aclaramos a Laravel el nombre exacto de la tabla por si las moscas
    protected $table = 'pedido_detalles';

    protected $fillable = [
        'pedido_id',
        'lona_id',
        'talla',
        'cantidad',
        'precio_unitario'
    ];

    /**
     * RELACIÓN: Este detalle pertenece a un Pedido padre (Muchos a 1)
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * RELACIÓN: Este detalle describe la compra de una Lona específica (Muchos a 1)
     */
    public function lona(): BelongsTo
    {
        return $this->belongsTo(Lona::class, 'lona_id');
    }
}
