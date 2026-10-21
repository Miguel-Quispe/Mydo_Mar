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
        // 1. Niveles de Dificultad
        Schema::create('muebles_niveles', function (Blueprint $table) {
            $table->increments('id_nivel_muebles');
            $table->integer('id_dim_tiempo_creacion');
            $table->string('nombre_nivel_muebles', 80)->unique();

            $table->foreign('id_dim_tiempo_creacion')
                  ->references('id_dim_tiempo')
                  ->on('dim_tiempo')
                  ->onUpdate('cascade');
        });

        // 2. Categorías de Muebles (Oficina, Cocina, etc.)
        Schema::create('muebles_categorias', function (Blueprint $table) {
            $table->increments('id_categoria_muebles');
            $table->integer('id_dim_tiempo_creacion');
            $table->string('nombre_categoria_muebles', 100)->unique();
            $table->string('icono_categoria_muebles', 100)->nullable();

            $table->foreign('id_dim_tiempo_creacion')
                  ->references('id_dim_tiempo')
                  ->on('dim_tiempo')
                  ->onUpdate('cascade');
        });

        // 3. Materiales de Melamina (18mm, Roble, Nogal, Blanco, etc.)
        Schema::create('muebles_materiales', function (Blueprint $table) {
            $table->increments('id_material_muebles');
            $table->integer('id_dim_tiempo_creacion');
            $table->string('nombre_material_muebles', 120);
            $table->string('tipo_material_muebles', 80)->default('Melamina');
            $table->decimal('espesor_material_muebles', 6, 2)->default(18.00);
            $table->decimal('precio_lamina_muebles', 10, 2)->default(0.00);

            $table->foreign('id_dim_tiempo_creacion')
                  ->references('id_dim_tiempo')
                  ->on('dim_tiempo')
                  ->onUpdate('cascade');
        });

        // 4. Catálogo Maestro de Herrajes Globales (BOM)
        Schema::create('muebles_catalogo_herrajes_global', function (Blueprint $table) {
            $table->increments('id_catalogo_herraje_global');
            $table->unsignedInteger('id_usuario_registra_users');
            $table->integer('id_dim_tiempo_creacion');
            $table->string('nombre_herraje_global', 120);
            $table->string('tipo_visibilidad_herraje', 40)->default('oculto');
            $table->decimal('precio_referencial', 10, 2)->default(0.00);
            $table->boolean('activo_global')->default(true);

            $table->foreign('id_usuario_registra_users')
                  ->references('id_usuario_users')
                  ->on('users_cuentas')
                  ->onDelete('restrict');

            $table->foreign('id_dim_tiempo_creacion')
                  ->references('id_dim_tiempo')
                  ->on('dim_tiempo')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('muebles_catalogo_herrajes_global');
        Schema::dropIfExists('muebles_materiales');
        Schema::dropIfExists('muebles_categorias');
        Schema::dropIfExists('muebles_niveles');
    }
};
