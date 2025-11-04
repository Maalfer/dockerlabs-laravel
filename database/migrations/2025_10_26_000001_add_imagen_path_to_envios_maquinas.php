<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('envios_maquinas', function (Blueprint $table) {
            if (!Schema::hasColumn('envios_maquinas', 'imagen_path')) {
                $table->string('imagen_path')->nullable()->after('enlace_descarga');
            }
        });
    }

    public function down(): void {
        Schema::table('envios_maquinas', function (Blueprint $table) {
            if (Schema::hasColumn('envios_maquinas', 'imagen_path')) {
                $table->dropColumn('imagen_path');
            }
        });
    }
};
