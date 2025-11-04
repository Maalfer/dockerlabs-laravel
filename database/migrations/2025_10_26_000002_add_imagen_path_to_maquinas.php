<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('maquinas', function (Blueprint $table) {
            if (!Schema::hasColumn('maquinas', 'imagen_path')) {
                $table->string('imagen_path')->nullable()->after('enlace_descarga');
            }
        });
    }

    public function down(): void {
        Schema::table('maquinas', function (Blueprint $table) {
            if (Schema::hasColumn('maquinas', 'imagen_path')) {
                $table->dropColumn('imagen_path');
            }
        });
    }
};
