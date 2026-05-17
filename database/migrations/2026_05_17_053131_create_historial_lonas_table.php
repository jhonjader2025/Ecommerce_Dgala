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
        Schema::create('historial_lonas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lona_id')
                ->constrained('lonas')
                ->onDelete('cascade');

            $table->unsignedBigInteger('orden_item_id')->nullable(); // Lo llenaremos cuando avancemos a ventas
            $table->enum('accion', ['descuento', 'ajuste_manual', 'ingreso', 'agotado']);
            $table->string('talla', 10)->nullable();
            $table->integer('cantidad_cambio')->nullable();
            $table->integer('cantidad_restante')->nullable();
            $table->json('snapshot_json')->nullable(); // Para guardar el estado exacto en texto JSON
            $table->text('notas')->nullable();

            // Llave foránea amarrada al usuario (administrador) que hizo el movimiento
            $table->foreignId('creado_por')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Si el admin se borra, el historial no se pierde

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_lonas');
    }
};
