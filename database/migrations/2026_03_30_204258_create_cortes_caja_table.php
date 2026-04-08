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
    Schema::create('cortes_caja', function (Blueprint $table) {
        $table->id();
        $table->foreignId('usuario_id')->constrained('usuarios');
        $table->timestamp('fecha_apertura');
        $table->timestamp('fecha_cierre')->useCurrent();
        $table->decimal('monto_inicial', 10, 2);
        $table->decimal('total_ventas_efectivo', 10, 2)->default(0);
        $table->decimal('total_ventas_tarjeta', 10, 2)->default(0);
        $table->decimal('total_esperado', 10, 2);
        $table->decimal('total_contado', 10, 2)->nullable();
        $table->decimal('difference', 10, 2)->nullable();
        $table->text('notas')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cortes_caja');
    }
};
