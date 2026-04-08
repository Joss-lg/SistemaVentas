<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Nombres técnicos definitivos
            $table->string('tipo_pago')->default('efectivo')->after('total');
            $table->string('referencia_pago')->nullable()->after('tipo_pago');
            $table->decimal('pago_cliente', 10, 2)->nullable()->after('referencia_pago');
            $table->decimal('cambio', 10, 2)->default(0)->after('pago_cliente');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['tipo_pago', 'referencia_pago', 'pago_cliente', 'cambio']);
        });
    }
};