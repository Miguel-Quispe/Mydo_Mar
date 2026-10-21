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
        // 1. Proyectos de Muebles Diseñados
        Schema::create('muebles_disenos', function (Blueprint $table) {
            $table->increments('id_diseno_muebles');
            $table->unsignedInteger('creador_id_usuario_users');
            $table->unsignedInteger('id_empresa_users')->nullable();
            $table->unsignedInteger('id_categoria_muebles');
            $table->unsignedInteger('id_nivel_muebles');
            $table->integer('id_dim_tiempo_publicacion');
            $table->string('nombre_diseno_muebles', 191);
            $table->boolean('publicado_diseno_muebles')->default(false);

            $table->foreign('creador_id_usuario_users')
                  ->references('id_usuario_users')
                  ->on('users_cuentas')
                  ->onDelete('restrict');

            $table->foreign('id_empresa_users')
                  ->references('id_empresa_users')
                  ->on('empresa_users')
                  ->onDelete('set null');

            $table->foreign('id_categoria_muebles')
                  ->references('id_categoria_muebles')
                  ->on('muebles_categorias')
                  ->onUpdate('cascade');

            $table->foreign('id_nivel_muebles')
                  ->references('id_nivel_muebles')
                  ->on('muebles_niveles')
                  ->onUpdate('cascade');

            $table->foreign('id_dim_tiempo_publicacion')
                  ->references('id_dim_tiempo')
                  ->on('dim_tiempo')
                  ->onUpdate('cascade');

            $table->index('id_empresa_users', 'idx_dis_empresa');
            $table->index('publicado_diseno_muebles', 'idx_dis_publicado');
        });

        // 2. Despiece Milimétrico de Piezas para Taller y Corte
        Schema::create('muebles_piezas', function (Blueprint $table) {
            $table->increments('id_pieza_muebles');
            $table->unsignedInteger('id_diseno_muebles');
            $table->unsignedInteger('id_material_muebles');
            $table->string('nombre_pieza_muebles', 120);
            $table->decimal('ancho_mm_muebles', 10, 2);
            $table->decimal('alto_mm_muebles', 10, 2);

            $table->foreign('id_diseno_muebles')
                  ->references('id_diseno_muebles')
                  ->on('muebles_disenos')
                  ->onDelete('cascade');

            $table->foreign('id_material_muebles')
                  ->references('id_material_muebles')
                  ->on('muebles_materiales')
                  ->onUpdate('cascade');

            $table->index('id_diseno_muebles', 'idx_pie_diseno');
        });

        // 3. Herrajes por Proyecto (Bill of Materials)
        Schema::create('muebles_herrajes_por_proyecto', function (Blueprint $table) {
            $table->increments('id_herraje_proyecto');
            $table->unsignedInteger('id_diseno_muebles');
            $table->unsignedInteger('id_catalogo_herraje_global');
            $table->integer('cantidad_necesaria_proyecto')->default(1);

            $table->foreign('id_diseno_muebles')
                  ->references('id_diseno_muebles')
                  ->on('muebles_disenos')
                  ->onDelete('cascade');

            $table->foreign('id_catalogo_herraje_global')
                  ->references('id_catalogo_herraje_global')
                  ->on('muebles_catalogo_herrajes_global')
                  ->onUpdate('cascade');
        });

        // 4. Pasos de Ensamblaje (Guía de Montaje)
        Schema::create('muebles_pasos_ensamblaje', function (Blueprint $table) {
            $table->increments('id_paso_muebles');
            $table->unsignedInteger('id_diseno_muebles');
            $table->integer('numero_paso_muebles');
            $table->string('titulo_paso_muebles', 191);

            $table->foreign('id_diseno_muebles')
                  ->references('id_diseno_muebles')
                  ->on('muebles_disenos')
                  ->onDelete('cascade');
        });

        // 5. Progreso de Usuario / Operario en el Armado
        Schema::create('muebles_progreso_usuario', function (Blueprint $table) {
            $table->increments('id_progreso_muebles');
            $table->unsignedInteger('usuario_id_users');
            $table->unsignedInteger('id_diseno_muebles');
            $table->integer('id_dim_tiempo_inicio');
            $table->integer('paso_actual_muebles')->default(1);
            $table->decimal('porcentaje_progreso_muebles', 5, 2)->default(0.00);
            $table->boolean('completado_muebles')->default(false);

            $table->foreign('usuario_id_users')
                  ->references('id_usuario_users')
                  ->on('users_cuentas')
                  ->onDelete('cascade');

            $table->foreign('id_diseno_muebles')
                  ->references('id_diseno_muebles')
                  ->on('muebles_disenos')
                  ->onDelete('cascade');

            $table->foreign('id_dim_tiempo_inicio')
                  ->references('id_dim_tiempo')
                  ->on('dim_tiempo')
                  ->onUpdate('cascade');

            $table->index(['usuario_id_users', 'id_diseno_muebles'], 'idx_prog_usuario_diseno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('muebles_progreso_usuario');
        Schema::dropIfExists('muebles_pasos_ensamblaje');
        Schema::dropIfExists('muebles_herrajes_por_proyecto');
        Schema::dropIfExists('muebles_piezas');
        Schema::dropIfExists('muebles_disenos');
    }
};
