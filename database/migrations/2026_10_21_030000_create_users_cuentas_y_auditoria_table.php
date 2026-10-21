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
        // 1. Cuentas de Usuarios Corporativos y Operativos
        Schema::create('users_cuentas', function (Blueprint $table) {
            $table->increments('id_usuario_users');
            $table->unsignedInteger('id_empresa_users')->nullable();
            $table->unsignedInteger('id_rol_users');
            $table->unsignedInteger('id_empresa_rol')->nullable();
            $table->integer('id_dim_tiempo_registro');
            $table->string('nombre_usuario_users', 100)->unique();
            $table->string('email_usuario_users', 191)->unique();
            $table->string('password_hash_users', 255);
            $table->boolean('cuenta_generada_por_empresa')->default(false);

            $table->foreign('id_empresa_users')
                  ->references('id_empresa_users')
                  ->on('empresa_users')
                  ->onDelete('set null');

            $table->foreign('id_rol_users')
                  ->references('id_rol_users')
                  ->on('users_roles')
                  ->onUpdate('cascade');

            $table->foreign('id_empresa_rol')
                  ->references('id_empresa_rol')
                  ->on('empresa_roles')
                  ->onDelete('set null');

            $table->foreign('id_dim_tiempo_registro')
                  ->references('id_dim_tiempo')
                  ->on('dim_tiempo')
                  ->onUpdate('cascade');

            $table->index('id_empresa_users', 'idx_usr_empresa');
            $table->index('id_rol_users', 'idx_usr_rol');
        });

        // 2. Auditoría Forense de Credenciales (Append-Only)
        Schema::create('users_credenciales_auditoria', function (Blueprint $table) {
            $table->bigIncrements('id_auditoria_credencial');
            $table->unsignedInteger('id_usuario_users');
            $table->unsignedInteger('id_usuario_modifica_users');
            $table->integer('id_dim_tiempo_evento');
            $table->string('tipo_cambio', 60);
            $table->string('valor_anterior_hash', 255)->nullable();
            $table->string('valor_nuevo_hash', 255)->nullable();

            $table->foreign('id_usuario_users')
                  ->references('id_usuario_users')
                  ->on('users_cuentas')
                  ->onDelete('cascade');

            $table->foreign('id_usuario_modifica_users')
                  ->references('id_usuario_users')
                  ->on('users_cuentas')
                  ->onDelete('restrict');

            $table->foreign('id_dim_tiempo_evento')
                  ->references('id_dim_tiempo')
                  ->on('dim_tiempo')
                  ->onUpdate('cascade');

            $table->index('id_usuario_users', 'idx_aud_usuario');
            $table->index('id_dim_tiempo_evento', 'idx_aud_tiempo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_credenciales_auditoria');
        Schema::dropIfExists('users_cuentas');
    }
};
