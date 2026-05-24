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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            // Relación con el usuario que compra (Llave foránea)
            // Si borramos el usuario, protegemos los pedidos dejando el registro histórico
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            // Totales de la compra
            $table->decimal('total', 10, 2);

            // Estado del pedido usando enum para controlar el flujo del uniforme
            $table->enum('estado', ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado'])->default('pendiente');

            // Datos de entrega (pueden venir de la relación direcciones o texto directo)
            $table->string('direccion_entrega', 500);

            $table->timestamps(); // created_at será la fecha de la compra
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
