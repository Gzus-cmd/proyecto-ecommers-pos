<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * On a fresh migration (migrate:fresh) the column is already user_id
     * from the updated create migration. This only runs on existing databases
     * that still have the old empleado_id column.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('ventas_fisicas', 'empleado_id')) {
            return;
        }

        // Drop the FK to empleados first
        Schema::table('ventas_fisicas', function (Blueprint $table) {
            $table->dropForeign(['empleado_id']);
        });

        // Rename the column and re-create FK to users
        Schema::table('ventas_fisicas', function (Blueprint $table) {
            $table->renameColumn('empleado_id', 'user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('ventas_fisicas', 'user_id')) {
            return;
        }

        Schema::table('ventas_fisicas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('ventas_fisicas', function (Blueprint $table) {
            $table->renameColumn('user_id', 'empleado_id');
        });
    }
};
