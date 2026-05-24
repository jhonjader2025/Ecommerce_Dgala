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
        Schema::create('pedido_detalles', function (Blueprint $table) {
            $table->id();

            // Amarra este detalle a la cabecera del pedido
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');

            // Amarra el detalle al producto (Lona)
            $table->foreignId('lona_id')->constrained('lonas')->onDelete('restrict');

            // Guardamos la talla específica que se pidió (ej: 'S', 'M', 'XL')
            $table->string('talla', 10);

            // Cantidad y precio histórico (por si el precio del uniforme cambia en el futuro)
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_detalles');
    }
};
