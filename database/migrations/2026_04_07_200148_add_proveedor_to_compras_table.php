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
    Schema::table('compras', function (Blueprint $table) {
        // Añadimos el campo proveedor después del ID o donde prefieras
        $table->string('proveedor')->nullable()->after('id');
    });
}

public function down(): void
{
    Schema::table('compras', function (Blueprint $table) {
        $table->dropColumn('proveedor');
    });
}
};
