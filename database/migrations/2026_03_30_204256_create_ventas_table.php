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
         Schema::create('ventas', function (Blueprint $table) {
    $table->id();
    $table->string('folio', 20)->unique();
    $table->timestamp('fecha')->useCurrent();
    $table->foreignId('usuario_id')->constrained('usuarios');
    $table->unsignedBigInteger('cliente_id')->nullable(); 
    $table->decimal('subtotal', 10, 2);
    $table->decimal('descuento', 10, 2)->default(0);
    $table->decimal('total', 10, 2);
    $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'credito']);
    $table->decimal('monto_recibido', 10, 2)->nullable();
    $table->decimal('cambio', 10, 2)->nullable();
    $table->enum('estado', ['completada', 'cancelada', 'en_espera'])->default('completada');
    $table->foreignId('cancelada_por')->nullable()->constrained('usuarios');
    $table->text('motivo_cancelacion')->nullable();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
