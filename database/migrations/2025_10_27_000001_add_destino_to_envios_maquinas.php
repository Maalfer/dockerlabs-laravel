<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('envios_maquinas', function (Blueprint $table) {
            if (!Schema::hasColumn('envios_maquinas', 'destino')) {
                $table->string('destino', 20)->default('dockerlabs')->after('imagen_path');
            }
        });
    }

    public function down(): void {
        Schema::table('envios_maquinas', function (Blueprint $table) {
            if (Schema::hasColumn('envios_maquinas', 'destino')) {
                $table->dropColumn('destino');
            }
        });
    }
};
