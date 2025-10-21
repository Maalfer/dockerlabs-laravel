<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('writeups_temporal', function (Blueprint $table) {
            if (!Schema::hasColumn('writeups_temporal', 'autor_email')) {
                $table->string('autor_email', 255)->nullable()->after('autor');
            }
            if (Schema::hasColumn('writeups_temporal', 'user_id')) {
                // Asegura que user_id puede ser null para invitados
                // OJO: en MySQL necesitarás modificar el campo; en SQLite puede requerir tabla temporal.
                try {
                    $table->unsignedBigInteger('user_id')->nullable()->change();
                } catch (\Throwable $e) {
                    // Si tu motor no soporta change(), documenta el ajuste manual aquí.
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('writeups_temporal', function (Blueprint $table) {
            if (Schema::hasColumn('writeups_temporal', 'autor_email')) {
                $table->dropColumn('autor_email');
            }
            // No revertimos nullable() de user_id para evitar pérdida.
        });
    }
};
