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
        Schema::table('muebles_disenos', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('nombre_diseno_muebles');
            $table->longText('configuracion_3d_json')->nullable()->after('descripcion');
            $table->longText('imagen_render_url')->nullable()->after('configuracion_3d_json');
            $table->decimal('precio_estimado_total', 10, 2)->default(0.00)->after('imagen_render_url');
        });

        Schema::table('muebles_piezas', function (Blueprint $table) {
            $table->decimal('espesor_mm_muebles', 6, 2)->default(18.00)->after('alto_mm_muebles');
            $table->decimal('posicion_x', 10, 2)->default(0.00)->after('espesor_mm_muebles');
            $table->decimal('posicion_y', 10, 2)->default(0.00)->after('posicion_x');
            $table->decimal('posicion_z', 10, 2)->default(0.00)->after('posicion_y');
            $table->decimal('rotacion_x', 8, 2)->default(0.00)->after('posicion_z');
            $table->decimal('rotacion_y', 8, 2)->default(0.00)->after('rotacion_x');
            $table->decimal('rotacion_z', 8, 2)->default(0.00)->after('rotacion_y');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muebles_piezas', function (Blueprint $table) {
            $table->dropColumn([
                'espesor_mm_muebles',
                'posicion_x', 'posicion_y', 'posicion_z',
                'rotacion_x', 'rotacion_y', 'rotacion_z'
            ]);
        });

        Schema::table('muebles_disenos', function (Blueprint $table) {
            $table->dropColumn([
                'descripcion',
                'configuracion_3d_json',
                'imagen_render_url',
                'precio_estimado_total'
            ]);
        });
    }
};
