<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Orden extends Model
{
    use HasFactory;

    // Le aclaramos a Laravel que la tabla en MySQL se llama ordenes
    protected $table = 'ordenes';

    protected $fillable = [
        'user_id',
        'cupon_id',
        'direccion_id',
        'numero_orden',
        'subtotal',
        'descuento',
        'total',
        'estado_pedido',
        'notas_internas'
    ];

    /**
     * RELACIÓN: La Orden pertenece a un Usuario (Muchos a 1)
     * * ¿Qué hace? Te dice qué cliente fue el que hizo esta compra.
     * Ejemplo en código: $orden->user->name;
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * RELACIÓN: La Orden pertenece a un Cupón (Muchos a 1 - Opcional)
     * * ¿Qué hace? Te permite saber qué descuento se aplicó en esta venta. Puede ser null.
     * Ejemplo en código: $orden->cupon->codigo;
     */
    public function cupon(): BelongsTo
    {
        return $this->belongsTo(Cupon::class, 'cupon_id');
    }

    /**
     * RELACIÓN: La Orden tiene muchos Ítems o detalles (1 a Muchos)
     * * ¿Qué hace? Saca el desglose de cuáles uniformes, qué tallas y cuántas cantidades se vendieron en esta orden.
     * Ejemplo en código: $orden->items;
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrdenItem::class, 'orden_id');
    }

    /**
     * RELACIÓN: La Orden tiene un Pago (1 a 1)
     * * ¿Qué hace? Conecta la compra con su respectivo recibo o transacción de pago.
     * Ejemplo en código: $orden->pago->estado_pago;
     */
    public function pág(): HasOne
    {
        return $this->hasOne(Pago::class, 'orden_id');
    }
}
