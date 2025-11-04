<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('maquinas', function (Blueprint $table) {
            // Datos del usuario que envió la máquina (opcionales por compatibilidad)
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->string('autor')->nullable()->after('user_id');
            $table->string('autor_email')->nullable()->after('autor');

            $table->index('user_id', 'idx_maquinas_user_id');
            $table->index('autor_email', 'idx_maquinas_autor_email');

            // Si quieres FK (opcional, por compatibilidad con datos existentes)
            // $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maquinas', function (Blueprint $table) {
            // Si añadiste FK, suéltala primero
            // $table->dropForeign(['user_id']);
            $table->dropIndex('idx_maquinas_user_id');
            $table->dropIndex('idx_maquinas_autor_email');
            $table->dropColumn(['user_id','autor','autor_email']);
        });
    }
};
