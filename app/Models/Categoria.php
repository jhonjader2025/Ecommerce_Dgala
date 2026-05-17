<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;

    // Le aclaramos a Laravel que la tabla en MySQL se llama categorias
    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'slug',
        'padre_id',
        'orden'
    ];

    /**
     * RELACIÓN: La categoría pertenece a una categoría Padre (Muchos a 1)
     * * ¿Qué hace? Si es una subcategoría (Ej: "Scrubs Quirúrgicos"), te permite saber cuál es su categoría principal (Ej: "Línea Médica").
     * Ejemplo en código: $subcategoria->padre->nombre;
     */
    public function padre(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'padre_id');
    }

    /**
     * RELACIÓN: Una categoría puede tener muchas Subcategorías (1 a Muchos)
     * * ¿Qué hace? Te permite listar todos los hijos o subniveles que tiene una categoría principal.
     * Ejemplo en código: $categoriaPadre->hijos; (Trae la colección de subcategorías).
     */
    public function hijos(): HasMany
    {
        return $this->hasMany(Categoria::class, 'padre_id');
    }

    /**
     * RELACIÓN: Una categoría tiene muchos Productos (1 a Muchos)
     * * ¿Qué hace? Te permite sacar de un solo golpe todos los uniformes que pertenecen a esta categoría.
     * Ejemplo en código: $categoria->productos; (Ideal para los filtros de la tienda virtual).
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }
}
