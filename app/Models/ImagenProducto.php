<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagenProducto extends Model
{
    use HasFactory;

    // Le aclaramos a Laravel que la tabla en MySQL se llama imagenes_producto
    protected $table = 'imagenes_producto';

    // Lista blanca para permitir asignación masiva de las URLs de las fotos
    protected $fillable = [
        'producto_id',
        'variante_id',
        'url',
        'es_portada',
        'orden'
    ];

    /**
     * RELACIÓN: La Imagen pertenece a un Producto (Muchos a 1)
     * * ¿Qué hace? Permite que si estás manipulando una foto suelta, puedas saber a qué uniforme de D'gala le pertenece.
     * Ejemplo en código: $imagen->producto->nombre;
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * RELACIÓN: La Imagen puede pertenecer a una Variante específica (Muchos a 1 - Opcional)
     * * ¿Qué hace? Conecta la foto con una combinación de talla y color exacta. Ideal para mostrar el uniforme azul cuando el cliente seleccione el color azul.
     * Ejemplo en código: $imagen->variante->sku; (Puede devolver null si es una foto general del producto).
     */
    public function variante(): BelongsTo
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_id');
    }
}
