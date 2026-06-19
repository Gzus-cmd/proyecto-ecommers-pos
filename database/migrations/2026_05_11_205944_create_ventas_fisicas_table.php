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
        Schema::create('ventas_fisicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('fecha_venta')->useCurrent();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuesto', 10, 2);
            $table->decimal('total', 10, 2);

            $table->foreignId('metodo_pago_id')->constrained('metodos_pago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas_fisicas');
    }
};
