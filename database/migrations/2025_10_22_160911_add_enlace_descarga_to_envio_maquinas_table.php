<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('envios_maquinas') && !Schema::hasColumn('envios_maquinas', 'enlace_descarga')) {
            Schema::table('envios_maquinas', function (Blueprint $table) {
                $table->string('enlace_descarga', 255)->nullable()->after('writeup');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('envios_maquinas') && Schema::hasColumn('envios_maquinas', 'enlace_descarga')) {
            Schema::table('envios_maquinas', function (Blueprint $table) {
                $table->dropColumn('enlace_descarga');
            });
        }
    }
};
