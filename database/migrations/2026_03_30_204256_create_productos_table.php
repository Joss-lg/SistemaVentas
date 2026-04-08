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
        Schema::create('productos', function (Blueprint $table) {
    $table->id();
    $table->string('codigo_barras', 50)->unique();
    $table->string('descripcion', 200);
    $table->decimal('precio_costo', 10, 2);
    $table->decimal('precio_venta', 10, 2);
    $table->decimal('stock_actual', 10, 3)->default(0);
    $table->decimal('stock_minimo', 10, 3)->default(0);
    $table->foreignId('departamento_id')->nullable()->constrained('departamentos');
    $table->boolean('es_granel')->default(false);
    $table->enum('unidad_medida', ['pieza', 'kg', 'litro', 'gramo'])->default('pieza');
    $table->boolean('activo')->default(true);
    $table->timestamp('fecha_creacion')->useCurrent();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
