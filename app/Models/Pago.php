<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'orden_id',
        'metodo_pago',
        'estado_pago',
        'transaccion_id',
        'monto',
        'fecha_pago'
    ];

    /**
     * RELACIÓN: El Pago pertenece a una Orden (1 a 1 invertido)
     * * ¿Qué hace? Te permite saber cuál es la orden de compra que saldó esta transacción financiera.
     * Ejemplo en código: $pago->orden->numero_orden;
     */
    public function orden(): BelongsTo
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }
}
