<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Direccion extends Model
{
    use HasFactory;

    // Le aclaramos a Laravel que la tabla real es direcciones en español
    protected $table = 'direcciones';

    protected $fillable = [
        'user_id',
        'direccion',
        'ciudad',
        'departamento',
        'codigo_postal',
        'telefono_contacto',
        'notas_entrega'
    ];

    /**
     * RELACIÓN: la Dirección pertenece a un Usuario (Muchos a 1)
     * * ¿Qué hace? Te permite saber a qué cliente le pertenece esta dirección de despacho.
     * Ejemplo en código: $direccion->user->name; (Saca el nombre del comprador).
     */
    public function user(): BelongsTo
    {
        // Se conecta con el modelo nativo de Laravel (User)
        return $this->belongsTo(User::class, 'user_id');
    }
}
