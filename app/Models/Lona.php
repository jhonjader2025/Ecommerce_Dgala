<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lona extends Model
{
    use HasFactory;

    // Le aclara a Laravel el nombre exacto de la tabla en MySQL
    protected $table = 'lonas';

    // Campos que se pueden llenar desde formularios o arreglos de forma masiva
    protected $fillable = [
        'dotacion_id',
        'codigo',
        'tipo_producto',
        'categoria',
        'color',
        'estado',
        'activa'
    ];

    /**
     * RELACIÓN: La Lona pertenece a una Dotación (Muchos a 1)
     * * ¿Qué hace? Es la inversa de la relación anterior. Permite saber de qué lote o dotación viene esta lona.
     * Ejemplo en código: $lona->dotacion->nombre; (Te dice el nombre de su dotación padre).
     */
    public function dotacion(): BelongsTo
    {
        // belongsTo recibe: (Clase_Padre, Llave_Foránea_En_Esta_Tabla)
        return $this->belongsTo(Dotacion::class, 'dotacion_id');
    }

    /**
     * RELACIÓN: Una Lona tiene muchas Tallas de stock (1 a Muchos)
     * * ¿Qué hace? Conecta la lona con su desglose de inventario por tallas (S, M, L).
     * Ejemplo en código: $lona->tallas; (Trae las cantidades que hay por cada talla para esta lona).
     */
    public function tallas(): HasMany
    {
        return $this->hasMany(LonaTalla::class, 'lona_id');
    }
}
