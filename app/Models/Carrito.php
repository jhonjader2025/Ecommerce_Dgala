<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrito extends Model
{
    use HasFactory;

    protected $table = 'carritos';

    protected $fillable = [
        'user_id',
        'session_id',
        'created_at',
        'updated_at'
    ];

    /**
     * RELACIÓN: El Carrito pertenece a un Usuario (Muchos a 1 - Opcional)
     * * ¿Qué hace? Vincula la bolsa de compras con el cliente logueado. Puede ser null si es un invitado.
     * Ejemplo en código: $carrito->user->name;
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * RELACIÓN: Un Carrito tiene muchos Ítems o productos agregados (1 a Muchos)
     * * ¿Qué hace? Te permite sacar la lista de todos los uniformes y cantidades que el cliente metió a la bolsa.
     * Ejemplo en código: $carrito->items;
     */
    public function items(): HasMany
    {
        return $this->hasMany(CarritoItem::class, 'carrito_id');
    }
}
