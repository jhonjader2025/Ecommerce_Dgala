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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('referencia_pago')->unique()->after('id');
            $table->enum('estado_pago', ['pendiente', 'aprobado', 'rechazado', 'declinado'])->default('pendiente')->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['referencia_pago', 'estado_pago']);
        });
    }
};
