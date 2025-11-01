<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bunker_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by'); // admin que lo crea
            $table->string('name')->nullable();       // etiqueta opcional
            $table->string('token_hash', 255);        // hash del token
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable(); // si quieres one-shot
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bunker_tokens');
    }
};
