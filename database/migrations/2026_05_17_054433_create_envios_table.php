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
        Schema::create('envios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('orden_id')
                ->constrained('ordenes')
                ->onDelete('restrict');

            $table->string('transportadora', 100)->nullable(); // Ej: Servientrega, Interrapidísimo
            $table->string('guia', 100)->nullable();
            $table->enum('estado', ['preparando', 'enviado', 'en_ruta', 'entregado', 'fallido'])->default('preparando');
            $table->date('fecha_entrega_estimada')->nullable();
            $table->timestamp('entregado_en')->nullable();
            $table->timestamps();

            $table->index('orden_id', 'idx_envio_orden');
            $table->index('estado', 'idx_estado_envio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};
