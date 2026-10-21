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
        // 1. Empresas B2B (Madeart & Aliados)
        Schema::create('empresa_users', function (Blueprint $table) {
            $table->increments('id_empresa_users');
            $table->integer('id_dim_tiempo_creacion');
            $table->string('razon_social_empresa_users', 191);
            $table->string('nit_empresa_users', 45)->unique();
            $table->string('telefono_whatsapp_empresa_users', 30)->nullable();
            $table->boolean('activa_empresa_users')->default(true);

            $table->foreign('id_dim_tiempo_creacion')
                  ->references('id_dim_tiempo')
                  ->on('dim_tiempo')
                  ->onUpdate('cascade');
                  
            $table->index('activa_empresa_users', 'idx_empresa_activa');
        });

        // 2. Subroles Operativos de Planta por Empresa (Armador, Diseñador, etc.)
        Schema::create('empresa_roles', function (Blueprint $table) {
            $table->increments('id_empresa_rol');
            $table->unsignedInteger('id_empresa_users');
            $table->integer('id_dim_tiempo_creacion');
            $table->string('nombre_rol_empresa', 80);
            $table->boolean('es_rol_predeterminado')->default(false);

            $table->foreign('id_empresa_users')
                  ->references('id_empresa_users')
                  ->on('empresa_users')
                  ->onDelete('cascade');

            $table->foreign('id_dim_tiempo_creacion')
                  ->references('id_dim_tiempo')
                  ->on('dim_tiempo')
                  ->onUpdate('cascade');

            $table->index('id_empresa_users', 'idx_emprol_empresa');
        });

        // 3. Roles Globales del Sistema (Admin Global, Gerente, Empleado, Alumno)
        Schema::create('users_roles', function (Blueprint $table) {
            $table->increments('id_rol_users');
            $table->string('nombre_rol_users', 80)->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_roles');
        Schema::dropIfExists('users_roles');
        Schema::dropIfExists('empresa_users');
    }
};
