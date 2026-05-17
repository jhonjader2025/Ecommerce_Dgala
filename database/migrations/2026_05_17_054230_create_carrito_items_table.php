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
        Schema::create('carrito_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('carrito_id')
                ->constrained('carritos')
                ->onDelete('cascade'); // Si se elimina el carrito, se van los ítems

            $table->foreignId('variante_id')
                ->constrained('variantes_producto')
                ->onDelete('cascade'); // Si la variante desaparece, se quita del carrito

            $table->foreignId('lona_id')
                ->nullable()
                ->constrained('lonas')
                ->onDelete('set null');

            $table->integer('cantidad')->default(1);
            $table->timestamps();

            // Evita que el mismo producto/variante se repita como dos filas en el mismo carrito
            $table->unique(['carrito_id', 'variante_id'], 'uq_carrito');

            // Índice de rendimiento para buscar rápido los ítems de un carrito
            $table->index('carrito_id', 'idx_carrito');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrito_items');
    }
};
