<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'rol',
        'telefono',
        'avatar_url',
        'activo', // ◄ Aquí está el interruptor clave
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * RELACIÓN: Un Usuario puede registrar muchas Direcciones (1 a Muchos)
     * * ¿Qué hace? Te permite listar todos los lugares de entrega que el cliente ha guardado en D'gala.
     * Ejemplo en código: $usuario->direcciones; (Devuelve todas sus casas/oficinas).
     */
    public function direcciones(): HasMany
    {
        return $this->hasMany(Direccion::class, 'user_id');
    }
}
