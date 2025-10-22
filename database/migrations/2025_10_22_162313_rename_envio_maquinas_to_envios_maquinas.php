<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('envio_maquinas') && !Schema::hasTable('envios_maquinas')) {
            Schema::rename('envio_maquinas', 'envios_maquinas');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('envios_maquinas') && !Schema::hasTable('envio_maquinas')) {
            Schema::rename('envios_maquinas', 'envio_maquinas');
        }
    }
};
