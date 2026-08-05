<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cortes_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->timestamp('fecha_apertura')->useCurrent();
            $table->timestamp('fecha_cierre')->nullable();

            // Montos
            $table->decimal('monto_inicial', 10, 2)->default(0);
            $table->decimal('total_ventas_efectivo', 10, 2)->default(0);
            $table->decimal('total_ventas_tarjeta', 10, 2)->default(0);
            $table->decimal('total_transferencia', 10, 2)->default(0);
            $table->decimal('total_esperado', 10, 2)->default(0);
            $table->decimal('total_contado', 10, 2)->nullable();
            $table->decimal('difference', 10, 2)->nullable();

            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cortes_caja');
    }
};