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
        Schema::create('orden_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('orden_id')
                ->constrained('ordenes')
                ->onDelete('cascade'); // Si se borra la orden (en pruebas), se van sus ítems

            $table->foreignId('variante_id')
                ->constrained('variantes_producto')
                ->onDelete('restrict');

            $table->unsignedBigInteger('lona_id_snapshot')->nullable(); // Captura de la lona usada
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('total_linea', 10, 2);
            $table->timestamps();

            $table->index('orden_id', 'idx_orden');
            $table->index('variante_id', 'idx_variante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_items');
    }
};
