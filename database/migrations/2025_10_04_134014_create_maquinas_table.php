<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDificultadColumnInMaquinasTable extends Migration
{
    public function up()
    {
        Schema::table('maquinas', function (Blueprint $table) {
            $table->string('dificultad')->nullable()->change();  // Asegúrate de que la columna sea nullable
        });
    }

    public function down()
    {
        Schema::table('maquinas', function (Blueprint $table) {
            $table->string('dificultad')->nullable(false)->change();  // Hacer la columna no nullable si es necesario
        });
    }
}
