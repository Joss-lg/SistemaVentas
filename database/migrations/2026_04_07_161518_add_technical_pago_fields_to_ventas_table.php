<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('ventas', function (Blueprint $table) {
        // Verificamos una por una si la columna ya existe antes de agregarla
        if (!Schema::hasColumn('ventas', 'tipo_pago')) {
            $table->string('tipo_pago')->default('efectivo')->after('total');
        }
        
        if (!Schema::hasColumn('ventas', 'referencia_pago')) {
            $table->string('referencia_pago')->nullable()->after('tipo_pago');
        }
        
        if (!Schema::hasColumn('ventas', 'pago_cliente')) {
            $table->decimal('pago_cliente', 10, 2)->nullable()->after('referencia_pago');
        }
        
        if (!Schema::hasColumn('ventas', 'cambio')) {
            $table->decimal('cambio', 10, 2)->default(0)->after('pago_cliente');
        }
    });
}

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['tipo_pago', 'referencia_pago', 'pago_cliente', 'cambio']);
        });
    }
};