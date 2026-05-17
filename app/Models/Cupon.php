<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cupon extends Model
{
    use HasFactory;

    // Le aclaramos a Laravel que la tabla real es cupones en español
    protected $table = 'cupones';

    protected $fillable = [
        'codigo',
        'tipo',
        'valor',
        'gasto_minimo',
        'limite_uso',
        'usado_veces',
        'fecha_inicio',
        'fecha_fin',
        'activo'
    ];

    /**
     * RELACIÓN: Un Cupón puede estar aplicado en muchas Órdenes (1 a Muchos)
     * * ¿Qué hace? Te permite auditar e historializar cuántas compras reales han redimido este descuento.
     * Ejemplo en código: $cupon->ordenes; (Trae todas las ventas donde se usó).
     */
    public function ordenes(): HasMany
    {
        return $this->hasMany(Orden::class, 'cupon_id');
    }
}
