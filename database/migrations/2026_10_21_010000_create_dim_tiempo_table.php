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
        Schema::create('dim_tiempo', function (Blueprint $table) {
            // Smart Key cronológica en formato YYYYMMDDHH (ej: 2026102108)
            $table->integer('id_dim_tiempo')->primary();
            $table->timestamp('fecha_hora_exacta');
            $table->smallInteger('anio');
            $table->smallInteger('mes');
            $table->smallInteger('dia');
            $table->smallInteger('hora');

            // Índices para reportes agregados instantáneos
            $table->index(['anio', 'mes'], 'idx_dim_tiempo_anio_mes');
            $table->index('fecha_hora_exacta', 'idx_dim_tiempo_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dim_tiempo');
    }
};
