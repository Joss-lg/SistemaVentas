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
        Schema::create('kardex', function (Blueprint $table) {
    $table->id();
    $table->foreignId('producto_id')->constrained('productos');
    $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste']);
    $table->decimal('cantidad', 10, 3);
    $table->decimal('precio_unitario', 10, 2)->nullable();
    $table->foreignId('usuario_id')->constrained('usuarios');
    $table->foreignId('venta_id')->nullable()->constrained('ventas');
    $table->string('referencia', 100)->nullable();
    $table->text('nota')->nullable();
    $table->timestamp('fecha')->useCurrent();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardex');
    }
};
