<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('maquinas_ediciones')) {
            Schema::create('maquinas_ediciones', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('maquina_id');
                $table->unsignedBigInteger('user_id')->nullable(); // por si más adelante permites invitados
                $table->string('estado', 20)->default('pendiente'); // pendiente|aprobada|rechazada
                $table->json('cambios'); // payload con los campos propuestos (nombre, descripcion, dificultad, enlace_descarga)
                $table->text('comentario')->nullable();
                $table->timestamps();

                $table->index(['maquina_id', 'estado']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinas_ediciones');
    }
};
