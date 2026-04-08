<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('compras', function (Blueprint $table) {
        $table->id();
        $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
        $table->decimal('cantidad', 10, 3); // Soporta kilos (ej. 0.500 kg)
        $table->decimal('costo_total', 10, 2); // Lo que salió de caja para el proveedor
        $table->string('metodo_pago')->default('efectivo'); // Para saber si salió de caja o fue crédito
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
