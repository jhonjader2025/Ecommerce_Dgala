<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'nombre',
        'slug',
        'descripcion',
        'precio_minorista',
        'precio_mayorista',
        'min_cantidad_mayorista',
        'publicado',
        'permitir_sin_stock'
    ];

    /**
     * RELACIÓN: El Producto pertenece a una Categoría (Muchos a 1)
     * * ¿Qué hace? Te dice a qué sección de la tienda pertenece el uniforme.
     * Ejemplo en código: $producto->categoria->nombre;
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * RELACIÓN: Un Producto tiene muchas Variantes (1 a Muchos)
     * * ¿Qué hace? Amarra el producto general con sus opciones específicas de venta (Talla S Azul, Talla M Azul, etc.).
     * Ejemplo en código: $producto->variantes; (Trae los SKUs, colores y precios extra).
     */
    public function variantes(): HasMany
    {
        return $this->hasMany(VarianteProducto::class, 'producto_id');
    }

    /**
     * RELACIÓN: Un Producto tiene muchas Imágenes (1 a Muchos)
     * * ¿Qué hace? Te permite jalar la galería de fotos del uniforme para la vitrina web.
     * Ejemplo en código: $producto->imagenes;
     */
    public function imagenes(): HasMany
    {
        return $this->hasMany(ImagenProducto::class, 'producto_id');
    }
}
