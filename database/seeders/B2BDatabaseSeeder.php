<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class B2BDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Registro inicial en dim_tiempo
        $tiempoId = 2026102108;
        DB::table('dim_tiempo')->updateOrInsert(
            ['id_dim_tiempo' => $tiempoId],
            [
                'fecha_hora_exacta' => '2026-10-21 08:00:00',
                'anio' => 2026,
                'mes' => 10,
                'dia' => 21,
                'hora' => 8
            ]
        );

        // 2. Roles del Sistema
        $roles = [
            ['id_rol_users' => 1, 'nombre_rol_users' => 'Admin Global'],
            ['id_rol_users' => 2, 'nombre_rol_users' => 'Gerente Empresa'],
            ['id_rol_users' => 3, 'nombre_rol_users' => 'Empleado'],
            ['id_rol_users' => 4, 'nombre_rol_users' => 'Alumno'],
        ];
        foreach ($roles as $r) {
            DB::table('users_roles')->updateOrInsert(['id_rol_users' => $r['id_rol_users']], $r);
        }

        // 3. Empresa B2B: Madeart Carpintería & Diseño S.R.L.
        DB::table('empresa_users')->updateOrInsert(
            ['id_empresa_users' => 1],
            [
                'id_dim_tiempo_creacion' => $tiempoId,
                'razon_social_empresa_users' => 'Madeart Carpintería & Diseño S.R.L.',
                'nit_empresa_users' => '1023849021',
                'telefono_whatsapp_empresa_users' => '+59178945612',
                'activa_empresa_users' => true
            ]
        );

        // 4. Subroles de Fábrica para Madeart
        $subroles = [
            ['id_empresa_rol' => 1, 'id_empresa_users' => 1, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_rol_empresa' => 'Armador de Muebles', 'es_rol_predeterminado' => true],
            ['id_empresa_rol' => 2, 'id_empresa_users' => 1, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_rol_empresa' => 'Diseñador 3D / Modulador', 'es_rol_predeterminado' => false],
            ['id_empresa_rol' => 3, 'id_empresa_users' => 1, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_rol_empresa' => 'Supervisor de Calidad', 'es_rol_predeterminado' => false],
            ['id_empresa_rol' => 4, 'id_empresa_users' => 1, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_rol_empresa' => 'Transporte y Montaje', 'es_rol_predeterminado' => false],
        ];
        foreach ($subroles as $sr) {
            DB::table('empresa_roles')->updateOrInsert(['id_empresa_rol' => $sr['id_empresa_rol']], $sr);
        }

        // 5. Usuario Inicial Gerente / Administrador Madeart
        DB::table('users_cuentas')->updateOrInsert(
            ['id_usuario_users' => 1],
            [
                'id_empresa_users' => 1,
                'id_rol_users' => 2,
                'id_empresa_rol' => 2,
                'id_dim_tiempo_registro' => $tiempoId,
                'nombre_usuario_users' => 'gerencia_madeart',
                'email_usuario_users' => 'gerencia@madeart.com',
                'password_hash_users' => Hash::make('adidas2019'),
                'cuenta_generada_por_empresa' => false
            ]
        );

        // 6. Niveles de Muebles
        $niveles = [
            ['id_nivel_muebles' => 1, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_nivel_muebles' => 'Básico / Principiante'],
            ['id_nivel_muebles' => 2, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_nivel_muebles' => 'Intermedio / Taller'],
            ['id_nivel_muebles' => 3, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_nivel_muebles' => 'Avanzado / Industrial'],
        ];
        foreach ($niveles as $n) {
            DB::table('muebles_niveles')->updateOrInsert(['id_nivel_muebles' => $n['id_nivel_muebles']], $n);
        }

        // 7. Categorías
        $categorias = [
            ['id_categoria_muebles' => 1, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_categoria_muebles' => 'Oficina y Escritorios', 'icono_categoria_muebles' => 'briefcase'],
            ['id_categoria_muebles' => 2, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_categoria_muebles' => 'Cocinas y Alacenas', 'icono_categoria_muebles' => 'utensils'],
            ['id_categoria_muebles' => 3, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_categoria_muebles' => 'Dormitorios y Closets', 'icono_categoria_muebles' => 'bed'],
            ['id_categoria_muebles' => 4, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_categoria_muebles' => 'Salas y Centros de TV', 'icono_categoria_muebles' => 'tv'],
        ];
        foreach ($categorias as $c) {
            DB::table('muebles_categorias')->updateOrInsert(['id_categoria_muebles' => $c['id_categoria_muebles']], $c);
        }

        // 8. Melaminas de 18mm
        $materiales = [
            ['id_material_muebles' => 1, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_material_muebles' => 'Melamina Roble Natural', 'tipo_material_muebles' => 'Melamina Veta Madera', 'espesor_material_muebles' => 18.00, 'precio_lamina_muebles' => 320.00],
            ['id_material_muebles' => 2, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_material_muebles' => 'Melamina Nogal Oscuro', 'tipo_material_muebles' => 'Melamina Veta Madera', 'espesor_material_muebles' => 18.00, 'precio_lamina_muebles' => 340.00],
            ['id_material_muebles' => 3, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_material_muebles' => 'Melamina Blanca Lisa', 'tipo_material_muebles' => 'Melamina Unicolor', 'espesor_material_muebles' => 18.00, 'precio_lamina_muebles' => 260.00],
            ['id_material_muebles' => 4, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_material_muebles' => 'Melamina Gris Grafito', 'tipo_material_muebles' => 'Melamina Unicolor', 'espesor_material_muebles' => 18.00, 'precio_lamina_muebles' => 290.00],
            ['id_material_muebles' => 5, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_material_muebles' => 'Melamina Mármol Calacatta', 'tipo_material_muebles' => 'Melamina Texturizada', 'espesor_material_muebles' => 18.00, 'precio_lamina_muebles' => 380.00],
            ['id_material_muebles' => 6, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_material_muebles' => 'Melamina Haya Natural', 'tipo_material_muebles' => 'Melamina Veta Madera', 'espesor_material_muebles' => 18.00, 'precio_lamina_muebles' => 310.00],
        ];
        foreach ($materiales as $m) {
            DB::table('muebles_materiales')->updateOrInsert(['id_material_muebles' => $m['id_material_muebles']], $m);
        }

        // 9. Catálogo de Herrajes Globales
        $herrajes = [
            ['id_catalogo_herraje_global' => 1, 'id_usuario_registra_users' => 1, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_herraje_global' => 'Bisagra Cangrejo Codo 0 (Recta)', 'tipo_visibilidad_herraje' => 'visible', 'precio_referencial' => 8.50, 'activo_global' => true],
            ['id_catalogo_herraje_global' => 2, 'id_usuario_registra_users' => 1, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_herraje_global' => 'Corredera Telescópica Pesada 45cm', 'tipo_visibilidad_herraje' => 'oculto', 'precio_referencial' => 28.00, 'activo_global' => true],
            ['id_catalogo_herraje_global' => 3, 'id_usuario_registra_users' => 1, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_herraje_global' => 'Sistema Unión Minifix con Perno y Tambor', 'tipo_visibilidad_herraje' => 'oculto', 'precio_referencial' => 3.50, 'activo_global' => true],
            ['id_catalogo_herraje_global' => 4, 'id_usuario_registra_users' => 1, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_herraje_global' => 'Tirador Perfil de Aluminio Negro 15cm', 'tipo_visibilidad_herraje' => 'visible', 'precio_referencial' => 15.00, 'activo_global' => true],
            ['id_catalogo_herraje_global' => 5, 'id_usuario_registra_users' => 1, 'id_dim_tiempo_creacion' => $tiempoId, 'nombre_herraje_global' => 'Tornillo Soberbio 4x50mm para Aglomerado', 'tipo_visibilidad_herraje' => 'oculto', 'precio_referencial' => 0.25, 'activo_global' => true],
        ];
        foreach ($herrajes as $h) {
            DB::table('muebles_catalogo_herrajes_global')->updateOrInsert(['id_catalogo_herraje_global' => $h['id_catalogo_herraje_global']], $h);
        }

        // 10. Sincronización de secuencias seriales en PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            $tablasSecuencias = [
                ['users_cuentas', 'id_usuario_users'],
                ['empresa_users', 'id_empresa_users'],
                ['empresa_roles', 'id_empresa_rol'],
                ['users_roles', 'id_rol_users'],
                ['muebles_niveles', 'id_nivel_muebles'],
                ['muebles_categorias', 'id_categoria_muebles'],
                ['muebles_materiales', 'id_material_muebles'],
                ['muebles_catalogo_herrajes_global', 'id_catalogo_herraje_global'],
            ];

            foreach ($tablasSecuencias as [$tabla, $columna]) {
                $max = DB::table($tabla)->max($columna);
                if ($max) {
                    DB::statement("SELECT setval(pg_get_serial_sequence('{$tabla}', '{$columna}'), {$max})");
                }
            }
        }
    }
}
