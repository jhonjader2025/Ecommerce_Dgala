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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('orden_id')
                ->constrained('ordenes')
                ->onDelete('restrict');

            $table->text('motivo');
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'resuelta'])->default('pendiente');
            $table->text('resolucion_admin')->nullable();

            // El administrador que atiende el caso
            $table->foreignId('resuelto_por')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
