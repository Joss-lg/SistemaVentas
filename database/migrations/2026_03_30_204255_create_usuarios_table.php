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
    Schema::create('usuarios', function (Blueprint $table) {
        $table->id();
        $table->string('nombre', 100);
        $table->string('username', 50)->unique();
        $table->string('password_hash', 255);
        $table->enum('rol', ['administrador', 'cajero','supervisor']);
        $table->boolean('activo')->default(true);
        $table->enum('tema', ['claro', 'oscuro'])->default('claro');
        
        $table->timestamp('fecha_creacion')->useCurrent();
        $table->timestamp('ultima_sesion')->nullable();
    });
}

public function down(): void
{
    Schema::dropIfExists('usuarios');
}
};