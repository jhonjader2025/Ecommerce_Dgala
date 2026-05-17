<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            // Llave foránea que conecta con las categorías
            $table->foreignId('categoria_id')
                ->nullable()
                ->constrained('categorias')
                ->onDelete('set null'); // Si se borra la categoría, el producto no se elimina

            $table->string('nombre', 150);
            $table->string('slug', 160)->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('precio_minorista', 10, 2);
            $table->decimal('precio_mayorista', 10, 2);
            $table->integer('min_cantidad_mayorista')->default(12);
            $table->boolean('publicado')->default(false);
            $table->boolean('permitir_sin_stock')->default(true);
            $table->timestamp('eliminado_en')->nullable(); // Borrado lógico
            $table->timestamps();
        });

        // Nota técnica: El índice FULLTEXT para el buscador que tenías en tu script
        // lo agregamos con una consulta nativa porque cambia según el motor de la DB.
        DB::statement('ALTER TABLE productos ADD FULLTEXT idx_productos_busqueda (nombre, descripcion)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
