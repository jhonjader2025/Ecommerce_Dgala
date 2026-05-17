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
        Schema::create('users', function (Blueprint $table) {
            // Campos nativos que Laravel necesita para el Login automático
            $table->id(); // Este será el id_usuario para sus llaves foráneas
            $table->string('name'); // Nombre completo
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // Nullable por si se registra con Google
            $table->rememberToken();
            $table->timestamps(); // Esto crea 'created_at' y 'updated_at' automáticamente

            // --- Los campos en español de su DB.txt ---
            $table->string('google_id', 100)->nullable()->unique();
            $table->enum('rol', ['cliente', 'admin', 'super_admin'])->default('cliente');
            $table->string('telefono', 20)->nullable();
            $table->string('avatar_url', 500)->nullable();
            $table->timestamp('eliminado_en')->nullable(); // Para el borrado lógico

            // Índice de rendimiento para buscar rápido por rol
            $table->index('rol', 'idx_usuarios_rol');
        });

        // Las otras tablas que trae Laravel por defecto las dejamos quieticas abajo...
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
        
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
