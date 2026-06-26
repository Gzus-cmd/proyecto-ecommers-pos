<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos_local', function (Blueprint $table) {
            $table->date('fecha_vencimiento')->nullable()->after('precio_venta');
        });
    }

    public function down(): void
    {
        Schema::table('productos_local', function (Blueprint $table) {
            $table->dropColumn('fecha_vencimiento');
        });
    }
};
