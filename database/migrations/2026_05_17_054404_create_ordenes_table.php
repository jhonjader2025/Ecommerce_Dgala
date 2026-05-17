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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();

            // Llave foránea amarrada a los usuarios nativos de Laravel
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict'); // No deja borrar un usuario si tiene compras asociadas

            // Llave foránea amarrada a sus direcciones
            $table->foreignId('direccion_id')
                ->constrained('direcciones')
                ->onDelete('restrict');

            // Llave foránea al cupón (opcional)
            $table->foreignId('cupon_id')
                ->nullable()
                ->constrained('cupones')
                ->onDelete('set null');

            $table->string('numero', 30)->unique(); // Código de factura (Ej: DG-0001)
            $table->enum('estado', ['pendiente', 'confirmada', 'procesando', 'enviado', 'entregado', 'cancelada', 'devuelta'])->default('pendiente');
            $table->enum('tipo_precio', ['minorista', 'mayorista'])->default('minorista');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('envio_costo', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->text('notas_cliente')->nullable();
            $table->timestamps();

            // Índices de rendimiento de su DB.txt
            $table->index('user_id', 'idx_usuario');
            $table->index('direccion_id', 'idx_direccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
