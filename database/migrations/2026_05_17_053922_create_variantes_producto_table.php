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
        Schema::create('variantes_producto', function (Blueprint $table) {
            $table->id();

            // Llave foránea al producto padre
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->onDelete('cascade'); // Si se borra el producto, se van sus variantes

            // Llave foránea a la lona (puede ser null si el producto no maneja lona directa)
            $table->foreignId('lona_id')
                ->nullable()
                ->constrained('lonas')
                ->onDelete('set null');

            $table->string('sku', 100)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('talla', 10)->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('precio_extra', 10, 2)->default(0);
            $table->timestamp('eliminado_en')->nullable(); // Borrado lógico
            $table->timestamps();

            // Restricción única: No repetir la misma combinación de producto, color y talla
            $table->unique(['producto_id', 'color', 'talla']);

            // Índices de rendimiento para los filtros de la tienda que tenía en su DB.txt
            $table->index(['color', 'talla', 'stock'], 'idx_filtros');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variantes_producto');
    }
};
