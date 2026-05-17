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
        Schema::create('imagenes_producto', function (Blueprint $table) {
            $table->id();

            // Relación con el producto obligatoria
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->onDelete('cascade');

            // Relación con la variante (opcional)
            $table->foreignId('variante_id')
                ->nullable()
                ->constrained('variantes_producto')
                ->onDelete('cascade');

            $table->string('url', 500);
            $table->boolean('es_portada')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes_producto');
    }
};
