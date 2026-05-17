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
        Schema::create('lonas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dotacion_id')
                ->constrained('dotaciones')
                ->onDelete('restrict');

            $table->string('codigo', 50)->unique();
            $table->string('tipo_producto', 80)->nullable();
            $table->string('categoria', 80)->nullable();
            $table->string('color', 50)->nullable();
            $table->enum('estado', ['nuevo', 'usado'])->default('nuevo');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lonas');
    }
};
