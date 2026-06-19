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
        Schema::create('lotes_local', function (Blueprint $table) {
            $table->id();

            $table->string('sku_producto');
            $table->foreign('sku_producto')->references('sku')->on('productos_local');

            $table->string('numero_lote');
            $table->date('fecha_vencimiento');
            $table->integer('cantidad_disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes_local');
    }
};
