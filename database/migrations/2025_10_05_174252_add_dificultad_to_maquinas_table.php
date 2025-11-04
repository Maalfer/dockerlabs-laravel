<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Si la tabla no existe, la creamos completa
        if (!Schema::hasTable('maquinas')) {
            Schema::create('maquinas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->text('descripcion');
                $table->string('dificultad', 20)->default('medio')->index();
                $table->timestamps();
            });
        } else {
            // Si ya existe la tabla, añadimos la columna solo si falta
            if (!Schema::hasColumn('maquinas', 'dificultad')) {
                Schema::table('maquinas', function (Blueprint $table) {
                    $table->string('dificultad', 20)->default('medio')->index();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('maquinas') && Schema::hasColumn('maquinas', 'dificultad')) {
            Schema::table('maquinas', function (Blueprint $table) {
                $table->dropColumn('dificultad');
            });
        }
    }
};
