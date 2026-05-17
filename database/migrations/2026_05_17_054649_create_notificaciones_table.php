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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();

            // Nullable si la notificación va dirigida a todos los administradores
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            $table->enum('tipo', ['stock_bajo', 'orden', 'sistema', 'marketing'])->default('sistema');
            $table->string('titulo', 200);
            $table->text('mensaje');
            $table->timestamp('leido_en')->nullable();

            // Doble check de auditoría que tenías en tu DB.txt
            $table->foreignId('confirmado_por')
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
        Schema::dropIfExists('notificaciones');
    }
};
