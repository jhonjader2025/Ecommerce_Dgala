<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dotacion extends Model
{
    use HasFactory;

    // Le dice a Laravel que no busque la tabla "dotacions", sino "dotaciones" en español
    protected $table = 'dotaciones';

    // Lista blanca de campos que Laravel permite registrar o editar en lote (Mass Assignment)
    protected $fillable = [
        'nombre',
        'descripcion',
        'min_lonas',
        'max_lonas',
        'lonas_activas',
        'alerta_enviada_en'
    ];

    /**
     * RELACIÓN: Una Dotación tiene muchas Lonas (1 a Muchos)
     * * ¿Qué hace? Permite que desde una dotación puedas jalar todas sus telas asociadas.
     * Ejemplo en código: $dotacion->lonas; (Trae una colección con todas sus lonas).
     */
    public function lonas(): HasMany
    {
        // hasMany recibe: (Clase_Relacionada, Llave_Foránea_En_La_Otra_Tabla)
        return $this->hasMany(Lona::class, 'dotacion_id');
    }
}
