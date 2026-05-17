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
        Schema::create('dotaciones', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('nombre', 120);
            $table->text('descripcion')->nullable();
            $table->unsignedTinyInteger('min_lonas')->default(3); // Control de mínimos
            $table->unsignedTinyInteger('max_lonas')->default(10); // Control de máximos
            $table->integer('lonas_activas')->default(0);
            $table->timestamp('alerta_enviada_en')->nullable(); // Para cuando se esté agotando
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dotaciones');
    }
};
