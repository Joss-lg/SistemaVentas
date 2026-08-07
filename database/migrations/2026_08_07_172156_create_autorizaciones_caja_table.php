<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autorizaciones_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corte_caja_id')->constrained('cortes_caja')->onDelete('cascade');
            $table->foreignId('usuario_solicita_id')->constrained('usuarios');
            $table->foreignId('usuario_autoriza_id')->nullable()->constrained('usuarios');
            $table->decimal('efectivo_real', 10, 2); // lo que contó el cajero al momento de pedir
            $table->decimal('faltante', 10, 2); // diferencia negativa, informativo
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autorizaciones_caja');
    }
};