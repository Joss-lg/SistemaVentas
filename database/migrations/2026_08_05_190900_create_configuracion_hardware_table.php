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
        Schema::create('configuracion_hardware', function (Blueprint $table) {
            $table->id();
            $table->string('impresora_nombre')->nullable(); // nombre exacto en Windows si es USB
            $table->enum('impresora_tipo', ['usb', 'red'])->default('usb');
            $table->string('impresora_ip')->nullable(); // si es de red
            $table->string('cajon_comando_apertura')->default('\x1B\x70\x00\x19\xFA'); // editable si la impresora usa otro código
            $table->boolean('bascula_activada')->default(false);
            $table->integer('bascula_baud_rate')->default(9600);
            $table->boolean('modo_simulado')->default(true); // true = sin hardware conectado todavía
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_hardware');
    }
};
