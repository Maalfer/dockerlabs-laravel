<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('writeups_temporal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maquina_id');
            $table->string('autor', 120);
            $table->string('enlace', 2048);
            $table->string('estado', 20)->default('pendiente'); // opcional
            $table->timestamps();

            $table->foreign('maquina_id')
                  ->references('id')->on('maquinas')
                  ->cascadeOnDelete();
            $table->index(['maquina_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('writeups_temporal');
    }
};