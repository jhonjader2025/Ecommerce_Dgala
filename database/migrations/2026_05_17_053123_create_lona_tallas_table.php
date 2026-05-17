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
        Schema::create('lona_tallas', function (Blueprint $table) {
            $table->id();

            // Llave foránea amarrada a lonas
            $table->foreignId('lona_id')
                ->constrained('lonas')
                ->onDelete('cascade'); // Si se borra la lona, se van sus tallas

            $table->string('talla', 10);
            $table->integer('cantidad')->default(0);
            $table->timestamps();

            // Llave única compuesta para evitar duplicados (Ej: Lona 1 - Talla M solo existe una vez)
            $table->unique(['lona_id', 'talla']);

            // Índice extra de rendimiento para las búsquedas que teníamos al final del script
            $table->index(['lona_id', 'talla'], 'idx_lona_talla');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lona_tallas');
    }
};
