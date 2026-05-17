<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carritos', function (Blueprint $table) {
            $table->id();

            // Relación con los usuarios nativos de Laravel (users)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade'); // Si se borra el usuario, se limpia su carrito

            $table->string('session_id', 100)->nullable(); // Para carritos de invitados

            // Relación con la tabla de cupones que creamos arriba
            $table->foreignId('cupon_id')
                ->nullable()
                ->constrained('cupones')
                ->onDelete('set null'); // Si el cupón se borra, no se rompe el carrito

            $table->timestamps();

            // Restricción única para que un usuario solo tenga un carrito activo en la base de datos
            $table->unique('user_id', 'uq_carrito_usuario');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carritos');
    }
};
