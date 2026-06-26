<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add lote_local_id to detalle_ventas (nullable)
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->foreignId('lote_local_id')->nullable()->after('producto_sku')
                ->constrained('lotes_local')->nullOnDelete();
        });

        // 2. Drop stock_local table
        Schema::dropIfExists('stock_local');

        // 3. Create v_stock_lotes view
        DB::statement("
            CREATE VIEW v_stock_lotes AS
            SELECT
                l.id,
                l.sku_producto,
                l.numero_lote,
                l.fecha_vencimiento,
                l.cantidad_disponible as cantidad_inicial,
                COALESCE(SUM(dv.cantidad), 0) as cantidad_vendida,
                (l.cantidad_disponible - COALESCE(SUM(dv.cantidad), 0)) as stock_actual
            FROM lotes_local l
            LEFT JOIN detalle_ventas dv ON dv.lote_local_id = l.id
            LEFT JOIN ventas_fisicas vf ON dv.venta_id = vf.id
            GROUP BY l.id, l.sku_producto, l.numero_lote, l.fecha_vencimiento, l.cantidad_disponible
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_stock_lotes");

        Schema::create('stock_local', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes');
            $table->foreignId('lote_local_id')->constrained('lotes_local');
            $table->integer('cantidad_disponible');
            $table->timestamps();
        });

        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropForeign(['lote_local_id']);
            $table->dropColumn('lote_local_id');
        });
    }
};
