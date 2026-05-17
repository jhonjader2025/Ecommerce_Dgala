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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // Llave foránea amarrada a la tabla 'users' nativa de Laravel
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade'); // Si se borra el usuario, se van sus direcciones

            $table->string('etiqueta', 50)->default('Casa');
            $table->string('departamento', 80);
            $table->string('ciudad', 80);
            $table->string('direccion', 250);
            $table->string('codigo_postal', 10)->nullable();
            $table->boolean('es_principal')->default(false); // TINYINT(1) equivalente
            $table->timestamp('eliminado_en')->nullable(); // Borrado lógico
            $table->timestamps(); // Nos da created_at y updated_at gratis
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};
