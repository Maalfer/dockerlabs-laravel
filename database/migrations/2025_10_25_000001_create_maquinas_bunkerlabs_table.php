<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('maquinas_bunkerlabs')) {
            Schema::create('maquinas_bunkerlabs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index('idx_mb_user_id');
                $table->string('autor')->nullable();
                $table->string('autor_email')->nullable()->index('idx_mb_autor_email');

                $table->string('nombre');
                $table->text('descripcion');
                $table->string('dificultad', 20)->default('medio')->index('idx_mb_dificultad');
                $table->string('enlace_descarga')->nullable();

                $table->timestamps();

                // Si algún día quieres FK:
                // $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('maquinas_bunkerlabs')) {
            Schema::drop('maquinas_bunkerlabs');
        }
    }
};
