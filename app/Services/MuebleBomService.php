<?php

namespace App\Services;

use App\Models\MuebleCatalogoHerrajeGlobal;
use App\Models\MuebleDiseno;
use App\Models\MuebleHerrajeProyecto;
use App\Models\MuebleMaterial;
use App\Models\MueblePasoEnsamblaje;
use App\Models\MueblePieza;
use Illuminate\Support\Facades\DB;

class MuebleBomService
{
    /**
     * Área en metros cuadrados de una plancha estándar de melamina (2750 x 1830 mm).
     */
    const AREA_PLANCHA_M2 = 5.0325;

    /**
     * Calcula la lista de materiales (BOM), requerimiento de tableros y costo total.
     */
    public function calcularBom(array $piezas, int $idMaterial = 1): array
    {
        $material = MuebleMaterial::find($idMaterial) ?? MuebleMaterial::first();
        $precioLamina = $material ? (float) $material->precio_lamina_muebles : 280.00;

        $m2Total = 0.0;
        $piezasProcesadas = [];

        foreach ($piezas as $idx => $p) {
            $ancho = (float) ($p['width'] ?? $p['ancho'] ?? $p['pw'] ?? $p['w'] ?? 500);
            $alto = (float) ($p['height'] ?? $p['alto'] ?? $p['ph'] ?? $p['h'] ?? 500);
            $espesor = (float) ($p['depth'] ?? $p['espesor'] ?? $p['thickness'] ?? $p['d'] ?? 18);
            $nombre = $p['name'] ?? $p['nombre'] ?? ('Pieza #' . ($idx + 1));
            
            $m2Pieza = ($ancho * $alto) / 1000000.0;
            $m2Total += $m2Pieza;

            $posX = (float) ($p['posicion_x'] ?? $p['x'] ?? ($p['pos'][0] ?? 0));
            $posY = (float) ($p['posicion_y'] ?? $p['y'] ?? ($p['pos'][1] ?? 0));
            $posZ = (float) ($p['posicion_z'] ?? $p['z'] ?? ($p['pos'][2] ?? 0));

            $rotX = (float) ($p['rotacion_x'] ?? $p['rotX'] ?? ($p['rot'][0] ?? 0));
            $rotY = (float) ($p['rotacion_y'] ?? $p['rotY'] ?? ($p['rot'][1] ?? 0));
            $rotZ = (float) ($p['rotacion_z'] ?? $p['rotZ'] ?? ($p['rot'][2] ?? 0));

            $piezasProcesadas[] = [
                'nombre' => $nombre,
                'ancho_mm' => $ancho,
                'alto_mm' => $alto,
                'espesor_mm' => $espesor,
                'm2' => round($m2Pieza, 4),
                'posicion_x' => $posX,
                'posicion_y' => $posY,
                'posicion_z' => $posZ,
                'rotacion_x' => $rotX,
                'rotacion_y' => $rotY,
                'rotacion_z' => $rotZ,
            ];
        }

        // Estimación de planchas considerando un 15% de desperdicio por cortes (kerf de sierra)
        $m2ConDesperdicio = $m2Total * 1.15;
        $planchasEstimadas = ceil($m2ConDesperdicio / self::AREA_PLANCHA_M2);
        if ($planchasEstimadas < 1 && count($piezas) > 0) {
            $planchasEstimadas = 1;
        }

        $costoMelamina = $planchasEstimadas * $precioLamina;

        // Estimación de herrajes base según el número de piezas
        $totalPiezas = count($piezas);
        $minifixRequeridos = max(4, $totalPiezas * 4);
        $tornillosRequeridos = max(8, $totalPiezas * 4);

        $herrajes = [
            ['id_herraje' => 3, 'nombre' => 'Sistema Minifix con Perno y Tambor', 'cantidad' => $minifixRequeridos, 'precio_unit' => 3.50],
            ['id_herraje' => 5, 'nombre' => 'Tornillo Soberbio 4x50mm', 'cantidad' => $tornillosRequeridos, 'precio_unit' => 0.25],
            ['id_herraje' => 4, 'nombre' => 'Tirador / Nivelador de Base', 'cantidad' => 4, 'precio_unit' => 12.00],
        ];

        $costoHerrajes = 0.0;
        foreach ($herrajes as &$h) {
            $subtotal = $h['cantidad'] * $h['precio_unit'];
            $h['subtotal'] = $subtotal;
            $costoHerrajes += $subtotal;
        }

        $costoTotalEstimado = $costoMelamina + $costoHerrajes;

        return [
            'material_id' => $material->id_material_muebles ?? 1,
            'material_nombre' => $material->nombre_material_muebles ?? 'Melamina Roble Natural',
            'total_piezas' => $totalPiezas,
            'm2_neto' => round($m2Total, 2),
            'm2_con_desperdicio' => round($m2ConDesperdicio, 2),
            'planchas_necesarias' => (int) $planchasEstimadas,
            'costo_melamina' => round($costoMelamina, 2),
            'costo_herrajes' => round($costoHerrajes, 2),
            'costo_total' => round($costoTotalEstimado, 2),
            'piezas' => $piezasProcesadas,
            'herrajes' => $herrajes,
        ];
    }

    /**
     * Guarda el proyecto 3D completo desglosando piezas, herrajes y pasos de taller.
     */
    public function guardarProyectoCompleto(array $datos): MuebleDiseno
    {
        return DB::transaction(function () use ($datos) {
            $smartKey = DimTiempoService::obtenerOCrearSmartKey();

            // 1. Cabecera del Diseño
            $diseno = MuebleDiseno::create([
                'creador_id_usuario_users' => $datos['creador_id'] ?? 1,
                'id_empresa_users' => $datos['empresa_id'] ?? 1,
                'id_categoria_muebles' => $datos['categoria_id'] ?? 1,
                'id_nivel_muebles' => $datos['nivel_id'] ?? 1,
                'id_dim_tiempo_publicacion' => $smartKey,
                'nombre_diseno_muebles' => $datos['nombre'],
                'descripcion' => $datos['descripcion'] ?? 'Proyecto de diseño 3D generado en Madeart.',
                'configuracion_3d_json' => is_string($datos['estructura_3d']) ? $datos['estructura_3d'] : json_encode($datos['estructura_3d']),
                'imagen_render_url' => $datos['imagen_render'] ?? null,
                'precio_estimado_total' => $datos['precio_total'] ?? 0.00,
                'publicado_diseno_muebles' => $datos['publicado'] ?? true,
            ]);

            // 2. Persistencia de Piezas Desglosadas
            if (!empty($datos['piezas']) && is_array($datos['piezas'])) {
                foreach ($datos['piezas'] as $p) {
                    MueblePieza::create([
                        'id_diseno_muebles' => $diseno->id_diseno_muebles,
                        'id_material_muebles' => $datos['material_id'] ?? 1,
                        'nombre_pieza_muebles' => $p['nombre'] ?? 'Pieza',
                        'ancho_mm_muebles' => $p['ancho_mm'] ?? 500,
                        'alto_mm_muebles' => $p['alto_mm'] ?? 500,
                        'espesor_mm_muebles' => $p['espesor_mm'] ?? 18,
                        'posicion_x' => $p['posicion_x'] ?? 0,
                        'posicion_y' => $p['posicion_y'] ?? 0,
                        'posicion_z' => $p['posicion_z'] ?? 0,
                        'rotacion_x' => $p['rotacion_x'] ?? 0,
                        'rotacion_y' => $p['rotacion_y'] ?? 0,
                        'rotacion_z' => $p['rotacion_z'] ?? 0,
                    ]);
                }
            }

            // 3. Persistencia de Herrajes Requeridos (BOM)
            if (!empty($datos['herrajes']) && is_array($datos['herrajes'])) {
                foreach ($datos['herrajes'] as $h) {
                    MuebleHerrajeProyecto::create([
                        'id_diseno_muebles' => $diseno->id_diseno_muebles,
                        'id_catalogo_herraje_global' => $h['id_herraje'] ?? 1,
                        'cantidad_necesaria_proyecto' => $h['cantidad'] ?? 1,
                    ]);
                }
            }

            // 4. Generación Automática de Pasos de Taller Estándar
            $pasosEstandar = [
                1 => 'Preparación de tablero base y fijación de niveladores / zócalo.',
                2 => 'Ensamble de laterales y divisiones internas mediante unión Minifix.',
                3 => 'Instalación de cubierta superior, ajuste de rigidez estructural y verificación de escuadra.',
            ];

            foreach ($pasosEstandar as $num => $titulo) {
                MueblePasoEnsamblaje::create([
                    'id_diseno_muebles' => $diseno->id_diseno_muebles,
                    'numero_paso_muebles' => $num,
                    'titulo_paso_muebles' => $titulo,
                ]);
            }

            return $diseno;
        });
    }
}
