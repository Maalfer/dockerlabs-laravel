<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('envios_maquinas')) {
            Schema::create('envios_maquinas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre_maquina', 150);
                $table->string('dificultad', 20); // compatible con SQLite
                $table->string('autor_nombre', 120);
                $table->string('autor_enlace', 255)->nullable();
                $table->date('fecha_creacion')->nullable();
                $table->string('writeup', 255)->nullable();
                $table->string('enlace_descarga', 255)->nullable();
                $table->timestamps();
            });
        } else {
            if (!Schema::hasColumn('envios_maquinas', 'enlace_descarga')) {
                Schema::table('envios_maquinas', function (Blueprint $table) {
                    $table->string('enlace_descarga', 255)->nullable()->after('writeup');
                });
            }
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
