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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('orden_id')
                ->constrained('ordenes')
                ->onDelete('restrict');

            $table->string('metodo', 50)->nullable(); // Ej: Tarjeta, Nequi, PSE
            $table->string('referencia_pasarela', 100)->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'reembolsado'])->default('pendiente');
            $table->decimal('monto', 10, 2);
            $table->timestamp('pagado_en')->nullable();
            $table->timestamps();

            $table->index('orden_id', 'idx_pago_orden');
            $table->index('estado', 'idx_estado_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
