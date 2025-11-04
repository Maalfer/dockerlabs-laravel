<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('writeups_temporal', function (Blueprint $table) {
            $table->unsignedBigInteger('writeup_id')->nullable()->after('id');
            $table->string('tipo', 20)->default('nuevo')->after('estado'); // 'nuevo' | 'edicion'
            $table->string('comentario', 500)->nullable()->after('enlace');

            $table->foreign('writeup_id')
                  ->references('id')->on('writeups')
                  ->cascadeOnDelete();

            $table->index(['writeup_id', 'tipo', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::table('writeups_temporal', function (Blueprint $table) {
            $table->dropForeign(['writeup_id']);
            $table->dropColumn(['writeup_id', 'tipo', 'comentario']);
        });
    }
};
