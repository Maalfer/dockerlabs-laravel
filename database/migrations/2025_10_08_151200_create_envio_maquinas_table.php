<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('envios_maquinas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_maquina');
            $table->enum('dificultad', ['facil', 'medio', 'dificil'])->default('medio');
            $table->string('autor_nombre');
            $table->string('autor_enlace')->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->string('writeup')->nullable(); // URL del writeup
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envios_maquinas');
    }
};
